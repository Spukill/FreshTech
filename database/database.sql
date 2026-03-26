-----------------------------------------
-- Prior cleanup
-----------------------------------------

DROP TYPE IF EXISTS orderval CASCADE;
DROP TYPE IF EXISTS repval CASCADE;

DROP TABLE IF EXISTS report_reviews CASCADE;
DROP TABLE IF EXISTS report_products CASCADE;
DROP TABLE IF EXISTS reports CASCADE;
DROP TABLE IF EXISTS reviews CASCADE;
DROP TABLE IF EXISTS cart_items CASCADE;
DROP TABLE IF EXISTS shopping_carts CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS promotions CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS wishlists CASCADE;
DROP TABLE IF EXISTS wishlist_products CASCADE;
DROP TABLE IF EXISTS product_specs CASCADE;
DROP TABLE IF EXISTS notifications CASCADE;
DROP TABLE IF EXISTS admins CASCADE;
DROP TABLE IF EXISTS buyers CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS order_status CASCADE;
DROP TABLE IF EXISTS product_available CASCADE;

DROP FUNCTION IF EXISTS delete_account() CASCADE;
DROP FUNCTION IF EXISTS review_product() CASCADE;
DROP FUNCTION IF EXISTS prevent_duplicate_wishlist() CASCADE;
DROP FUNCTION IF EXISTS review_only_delivered() CASCADE;
DROP FUNCTION IF EXISTS assure_timestamp() CASCADE;
DROP FUNCTION IF EXISTS product_search_update() CASCADE;
DROP FUNCTION IF EXISTS create_order() CASCADE;

-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE orderval AS ENUM ('cancelled', 'in distribution', 'delivered');
CREATE TYPE repval AS ENUM ('rejected', 'pending', 'accepted');

-----------------------------------------
-- Tables
-----------------------------------------

-- We ended up using "users" for the user table since "user" is a reserved keyword in PostgreSQL
-- users table (R01) 
CREATE TABLE users (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    google_id VARCHAR
);

-- admin table (R02)
CREATE TABLE admins (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_user INTEGER NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- buyer table (R03)
CREATE TABLE buyers (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_user INTEGER NOT NULL REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    user_name TEXT NOT NULL,
    exp INTEGER NOT NULL DEFAULT 0,
    profile_image VARCHAR
);

-- category table (R04)
CREATE TABLE categories (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    name TEXT NOT NULL,
    description TEXT NOT NULL
);

-- product table (R05)
-- NUMERIC(10,2) -> 10 max total digits with 2 decimal digits
CREATE TABLE products (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    name TEXT NOT NULL,
    description TEXT,
    price NUMERIC(10,2) NOT NULL DEFAULT 0 CHECK (price >= 0),
    stock INTEGER NOT NULL DEFAULT 0 CHECK (stock >= 0),
    image1 TEXT,
    image2 TEXT,
    image3 TEXT,
    id_category INTEGER NOT NULL REFERENCES categories(id) ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE promotions (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    amount INTEGER NOT NULL CHECK (amount > 0 AND amount < 100),
    level_limit INTEGER NOT NULL CHECK (level_limit >= 0 AND level_limit <= 5),
    id_product INTEGER NOT NULL REFERENCES products(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- shopping_cart table (R06)
CREATE TABLE shopping_carts (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_buyer INTEGER NOT NULL REFERENCES buyers(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- orders table (R07)
CREATE TABLE orders (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_cart INTEGER REFERENCES shopping_carts(id) ON UPDATE CASCADE ON DELETE CASCADE,
    status orderval NOT NULL,
    date_ord TIMESTAMP WITH TIME ZONE NOT NULL
);

-- cart items (R08)
CREATE TABLE cart_items (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_shopping_cart INTEGER NOT NULL REFERENCES shopping_carts(id) ON UPDATE CASCADE ON DELETE CASCADE,
    id_product INTEGER NOT NULL REFERENCES products(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    quantity INTEGER NOT NULL DEFAULT 1 CHECK (quantity >= 1)
);

-- review table (R09)
CREATE TABLE reviews (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_order INTEGER REFERENCES orders(id) ON UPDATE CASCADE ON DELETE SET NULL,
    id_product INTEGER NOT NULL REFERENCES products(id) ON UPDATE CASCADE ON DELETE CASCADE,
    title TEXT,
    description TEXT,
    rating INTEGER NOT NULL CHECK (rating BETWEEN 1 AND 5),
    time_stamp TIMESTAMP WITHOUT TIME ZONE
);

-- report table (R10)
CREATE TABLE reports (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_buyer INTEGER REFERENCES buyers(id) ON UPDATE CASCADE ON DELETE SET NULL,
    description TEXT,
    status repval NOT NULL
);

-- report_review table (R11)
CREATE TABLE report_reviews (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_report INTEGER NOT NULL REFERENCES reports(id) ON UPDATE CASCADE ON DELETE CASCADE,
    id_review INTEGER NOT NULL REFERENCES reviews(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- report_product table (R12)
CREATE TABLE report_products (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_report INTEGER NOT NULL REFERENCES reports(id) ON UPDATE CASCADE ON DELETE CASCADE,
    id_product INTEGER NOT NULL REFERENCES products(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- wishlist table (R13)
CREATE TABLE wishlists (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_buyer INTEGER NOT NULL REFERENCES buyers(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- wishlist_product (R14)
CREATE TABLE wishlist_products (
    id_wishlist INTEGER NOT NULL REFERENCES wishlists(id) ON UPDATE CASCADE ON DELETE CASCADE,
    id_product INTEGER NOT NULL REFERENCES products(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    PRIMARY KEY (id_wishlist, id_product)
);

-- product_spec (R15)
CREATE TABLE product_specs (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_product INTEGER NOT NULL REFERENCES products(id) ON UPDATE CASCADE ON DELETE CASCADE,
    spec_key TEXT NOT NULL,
    spec_value TEXT NOT NULL
);

-- notification (R16)
CREATE TABLE notifications (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_buyer INTEGER REFERENCES buyers(id) ON UPDATE CASCADE ON DELETE SET NULL,
    title TEXT NOT NULL,
    date_not TIMESTAMP WITH TIME ZONE NOT NULL,
    viewed BOOLEAN NOT NULL DEFAULT FALSE
);

-- order_status table (R17)
CREATE TABLE order_status (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_notification INTEGER REFERENCES notifications(id) ON UPDATE CASCADE ON DELETE SET NULL,
    id_order INTEGER NOT NULL REFERENCES orders(id) ON UPDATE CASCADE ON DELETE CASCADE,
    id_buyer INTEGER NOT NULL REFERENCES buyers(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-- product_available table (R18)
CREATE TABLE product_available (
    id INTEGER PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
    id_notification INTEGER REFERENCES notifications(id) ON UPDATE CASCADE ON DELETE SET NULL,
    id_buyer INTEGER NOT NULL REFERENCES buyers(id) ON UPDATE CASCADE ON DELETE CASCADE,
    id_product INTEGER NOT NULL REFERENCES products(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-----------------------------------------
-- Indexes
-----------------------------------------

-- IDX01 – product name search
CREATE INDEX product_name ON products USING btree (name);

-- IDX02 – buyer order history (now valid!)
CREATE INDEX user_orderstatus ON order_status USING btree (id_buyer);

-- IDX03 – reviews per product
CREATE INDEX product_review ON reviews USING btree (id_product);

-----------------------------------------
-- FTS Indexes
-----------------------------------------

-- Add a column to store computed tsvectors for full-text search
ALTER TABLE products
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update tsvectors
CREATE FUNCTION product_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
    NEW.tsvectors = (
      setweight(to_tsvector('english', NEW.name), 'A') ||
      setweight(to_tsvector('english', COALESCE(NEW.description, '')), 'B')
    );
  ELSIF TG_OP = 'UPDATE' THEN
    IF (NEW.name != OLD.name OR NEW.description != OLD.description) THEN
      NEW.tsvectors = (
        setweight(to_tsvector('english', NEW.name), 'A') ||
        setweight(to_tsvector('english', COALESCE(NEW.description, '')), 'B')
      );
    END IF;
  END IF;
  RETURN NEW;
END $$ 
LANGUAGE plpgsql;

-- Create a trigger before insert or update on Product
CREATE TRIGGER product_fts_update_trigger
BEFORE INSERT OR UPDATE ON products
FOR EACH ROW
EXECUTE FUNCTION product_search_update();

-- Finally, create a GIN index on the tsvectors column
CREATE INDEX idx_product_fts ON products USING GIN (tsvectors);


-----------------------------------------
-- Triggers
-----------------------------------------

-- Ensure user's reviews are kept on the system after account deletion (BR02)
CREATE FUNCTION delete_account() RETURNS TRIGGER AS
$BODY$
BEGIN
    UPDATE shopping_carts SET id_buyer = 1 WHERE id_buyer = OLD.id;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER delete_account
    BEFORE DELETE ON users
    FOR EACH ROW
    EXECUTE PROCEDURE delete_account();


-- Ensures a product can only be reviewed once per user (BR04)
CREATE OR REPLACE FUNCTION review_product()
RETURNS TRIGGER AS
$BODY$
BEGIN
    IF TG_OP = 'INSERT' THEN
        IF EXISTS (
            SELECT 1
            FROM reviews
            WHERE id_order = NEW.id_order
              AND id_product = NEW.id_product
        ) THEN
            RAISE EXCEPTION 'A Product can only be reviewed once.';
        END IF;
    END IF;

    RETURN NEW;
END;
$BODY$
LANGUAGE plpgsql;


CREATE TRIGGER review_product
        BEFORE INSERT OR UPDATE ON reviews
        FOR EACH ROW
        EXECUTE PROCEDURE review_product();

-- Ensures a buyer cannot add the duplicates of a product to their wishlist (BR05)
CREATE FUNCTION prevent_duplicate_wishlist() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM wishlist_products
        WHERE id_wishlist = NEW.id_wishlist
          AND id_product = NEW.id_product
    ) THEN
        RAISE EXCEPTION 'This product is already in the wishlist.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER prevent_duplicate_wishlist
BEFORE INSERT ON wishlist_products
FOR EACH ROW
EXECUTE PROCEDURE prevent_duplicate_wishlist();


-- Ensure that a buyer can only review a product after their order is marked as delivered. (BR06)
CREATE FUNCTION review_only_delivered() RETURNS TRIGGER AS
$BODY$
DECLARE
    order_status orderval;
BEGIN
    SELECT status INTO order_status
    FROM orders
    WHERE id = NEW.id_order;

    IF order_status IS NULL THEN
        RAISE EXCEPTION 'The order does not exist.';
    ELSIF order_status != 'delivered' THEN
        RAISE EXCEPTION 'Cannot review a product before the order is delivered.';
    END IF;

    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER review_only_delivered
BEFORE INSERT ON reviews
FOR EACH ROW
EXECUTE PROCEDURE review_only_delivered();


-- Ensures review timestamps are normalized (to GMT+1) (BR07)
CREATE FUNCTION assure_timestamp() RETURNS TRIGGER AS
$BODY$
BEGIN
    NEW.time_stamp := (NEW.time_stamp AT TIME ZONE 'UTC') AT TIME ZONE 'GMT+1';
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER assure_timestamp
    BEFORE INSERT ON reviews
    FOR EACH ROW
    EXECUTE PROCEDURE assure_timestamp();

-- Ensures review timestamps are normalized (to GMT+1) (BR07)
CREATE FUNCTION create_order() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NOT EXISTS (SELECT * FROM cart_items WHERE NEW.id_cart = id_shopping_cart) THEN
      RAISE EXCEPTION 'Can`t place order on empty cart.';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER create_order
    BEFORE INSERT ON orders
    FOR EACH ROW
    EXECUTE PROCEDURE create_order();
