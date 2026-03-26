TRUNCATE users, admins, buyers, categories, products, orders, shopping_carts, cart_items,
reviews, reports, report_reviews, report_products, wishlists, wishlist_products,
product_specs, notifications, order_status, product_available RESTART IDENTITY CASCADE;

-----------------------------------------
-- POPULATION SCRIPT
-----------------------------------------

-- USERS
INSERT INTO users (email, password) VALUES
('default@system.com', '$2y$12$/GfYI9SzrAyCtYu49Y8F2OBVN9kgGBOk1yvTzGG6B0jLwzlxOAXq.'),   -- id = 1 (default buyer) pass: 'defaultpass'
('admin@shop.com', '$2y$12$/M1tWKb.fCMQtYomN8J0JOC9Zak1HjPfG7q6WHe1lPtgCAU63MaOG'),     -- pass: 'admin123'
('buyer1@shop.com', '$2y$12$EZ0IzICmqi4mnD8Mg8vBEuzqnRMhSUxxoZenx6jzOA0Ww2LBTcYb6'),     -- pass: 'buyer123'
('buyer2@shop.com', '$2y$12$jZBxoQV7wepxdtjlRyNVD.QWeHL74..2WSXGKO/YpaH9uv2mpZQJi');     -- pass: 'buyer456'

-- ADMINS
INSERT INTO admins (id_user) VALUES
(2); -- admin user

-- BUYERS
INSERT INTO buyers (id_user, user_name, exp) VALUES
(1, 'SystemDefault', 0),   -- id = 1 (default buyer)
(3, 'Alice', 150),
(4, 'Bob', 300);

-- CATEGORIES
INSERT INTO categories (name, description) VALUES
('Gaming Desktops', 'High-performance gaming computers'),
('Laptops', 'Portable laptops and notebooks'),
('Smartphones', 'Mobile phones and accessories'),
('Components', 'Computer components and parts'),
('Storage', 'Storage devices and solutions'),
('Televisions', 'TVs and display devices'),
('Household appliances', 'Large household appliances'),
('Smart Home', 'Smart home devices and lighting'),
('Games and Toys', 'Games and toys');

-- PRODUCTS
INSERT INTO products (name, description, price, stock, image1, image2, image3, id_category) VALUES
('Gaming PC', 'High-end gaming desktop with RTX 4080', 2499.99, 5, 'gaming_pc_1.jpg', 'gaming_pc_2.jpg', 'gaming_pc_3.jpg', 1),
('Laptop', 'Lightweight laptop with 16GB RAM', 999.99, 10, 'laptop_1.jpg', 'laptop_2.jpg', 'laptop_3.jpg', 2),
('Smartphone', 'Latest 5G phone with OLED display', 699.99, 20, 'smartphone_1.jpg', 'smartphone_2.jpg', 'smartphone_3.jpg', 3),
('Graphics Card RTX 4080', 'NVIDIA GeForce RTX 4080 GPU', 1199.99, 8, 'rtx4080_1.jpg', 'rtx4080_2.jpg', 'rtx4080_3.jpg', 4),
('1TB SSD', 'Fast NVMe SSD storage', 149.99, 50, 'ssd_1.jpg', 'ssd_2.jpg', 'ssd_3.jpg', 5),
('65" 4K TV', 'Ultra HD Smart TV with HDR', 899.99, 15, 'tv_1.jpg', 'tv_2.jpg', 'tv_3.jpg', 6),
('Refrigerator', 'Energy-efficient fridge with ice maker', 799.99, 3, 'fridge_1.jpg', 'fridge_2.jpg', 'fridge_3.jpg', 7),
('Blender', 'High-speed kitchen blender', 89.99, 25, 'blender_1.jpg', 'blender_2.jpg', 'blender_3.jpg', 7),
('Smart LED Bulb', 'WiFi-enabled color-changing bulb', 29.99, 100, 'smart_bulb_1.jpg', 'smart_bulb_2.jpg', 'smart_bulb_3.jpg', 8),
('PlayStation 5', 'Next-gen gaming console', 499.99, 12, 'ps5_1.jpg', 'ps5_2.jpg', 'ps5_3.jpg', 9),
('Gaming Mouse', 'High-precision gaming mouse', 79.99, 30, 'gaming_mouse_1.jpg', 'gaming_mouse_2.jpg', 'gaming_mouse_3.jpg', 9),
('Mechanical Keyboard', 'RGB mechanical keyboard', 129.99, 20, 'keyboard_1.jpg', 'keyboard_2.jpg', 'keyboard_3.jpg', 4),
('Wireless Headphones', 'Noise-cancelling wireless headphones', 199.99, 15, 'headphones_1.jpg', 'headphones_2.jpg', 'headphones_3.jpg', 4),
('Monitor 27"', '4K gaming monitor', 399.99, 10, 'monitor_1.jpg', 'monitor_2.jpg', 'monitor_3.jpg', 6),
('Router', 'High-speed WiFi router', 149.99, 25, 'router_1.jpg', 'router_2.jpg', 'router_3.jpg', 4),
('External HDD 2TB', 'Portable external hard drive', 89.99, 40, 'hdd_1.jpg', 'hdd_2.jpg', 'hdd_3.jpg', 5),
('Webcam', 'HD webcam for streaming', 59.99, 35, 'webcam_1.jpg', 'webcam_2.jpg', 'webcam_3.jpg', 4),
('Microphone', 'USB condenser microphone', 99.99, 20, 'microphone_1.jpg', 'microphone_2.jpg', 'microphone_3.jpg', 4),
('Tablet', '10-inch Android tablet', 299.99, 18, 'tablet_1.jpg', 'tablet_2.jpg', 'tablet_3.jpg', 2),
('Smartwatch', 'Fitness tracking smartwatch', 249.99, 22, 'smartwatch_1.jpg', 'smartwatch_2.jpg', 'smartwatch_3.jpg', 3),
('E-reader', 'E-ink e-reader with backlight', 129.99, 28, 'ereader_1.jpg', 'ereader_2.jpg', 'ereader_3.jpg', 2),
('Drone', 'Quadcopter drone with camera', 499.99, 8, 'drone_1.jpg', 'drone_2.jpg', 'drone_3.jpg', 9),
('VR Headset', 'Virtual reality headset', 349.99, 12, 'vr_1.jpg', 'vr_2.jpg', 'vr_3.jpg', 9),
('Projector', 'HD home projector', 599.99, 6, 'projector_1.jpg', 'projector_2.jpg', 'projector_3.jpg', 6),
('Soundbar', '5.1 soundbar for TV', 179.99, 14, 'soundbar_1.jpg', 'soundbar_2.jpg', 'soundbar_3.jpg', 6),
('Coffee Maker', 'Programmable coffee maker', 119.99, 20, 'coffeemaker_1.jpg', 'coffeemaker_2.jpg', 'coffeemaker_3.jpg', 7),
('Air Fryer', 'Healthy air fryer oven', 149.99, 16, 'airfryer_1.jpg', 'airfryer_2.jpg', 'airfryer_3.jpg', 7),
('Robot Vacuum', 'Smart robot vacuum cleaner', 399.99, 9, 'robotvacuum_1.jpg', 'robotvacuum_2.jpg', 'robotvacuum_3.jpg', 8),
('Security Camera', 'Wireless security camera', 79.99, 30, 'securitycam_1.jpg', 'securitycam_2.jpg', 'securitycam_3.jpg', 8),
('Smart Thermostat', 'WiFi thermostat', 199.99, 12, 'thermostat_1.jpg', 'thermostat_2.jpg', 'thermostat_3.jpg', 8),
('Fitness Tracker', 'Wearable fitness band', 99.99, 25, 'fitnesstracker_1.jpg', 'fitnesstracker_2.jpg', 'fitnesstracker_3.jpg', 3),
('Bluetooth Speaker', 'Portable Bluetooth speaker', 49.99, 40, 'speaker_1.jpg', 'speaker_2.jpg', 'speaker_3.jpg', 4),
('Power Bank', '20000mAh power bank', 39.99, 50, 'powerbank_1.jpg', 'powerbank_2.jpg', 'powerbank_3.jpg', 3),
('USB Cable', 'Fast charging USB cable', 9.99, 100, 'usbcable_1.jpg', 'usbcable_2.jpg', 'usbcable_3.jpg', 4),
('Mouse Pad', 'Large gaming mouse pad', 19.99, 60, 'mousepad_1.jpg', 'mousepad_2.jpg', 'mousepad_3.jpg', 4),
('Desk Lamp', 'LED desk lamp with USB', 29.99, 35, 'desklamp_1.jpg', 'desklamp_2.jpg', 'desklamp_3.jpg', 8),
('Fan', 'Quiet desk fan', 24.99, 45, 'fan_1.jpg', 'fan_2.jpg', 'fan_3.jpg', 7),
('Heater', 'Electric space heater', 69.99, 20, 'heater_1.jpg', 'heater_2.jpg', 'heater_3.jpg', 7),
('Toaster', '4-slice toaster', 39.99, 30, 'toaster_1.jpg', 'toaster_2.jpg', 'toaster_3.jpg', 7),
('Microwave', 'Compact microwave oven', 89.99, 15, 'microwave_1.jpg', 'microwave_2.jpg', 'microwave_3.jpg', 7),
('Washing Machine', 'Front-load washing machine', 699.99, 4, 'washingmachine_1.jpg', 'washingmachine_2.jpg', 'washingmachine_3.jpg', 7),
('Dishwasher', 'Energy-efficient dishwasher', 599.99, 5, 'dishwasher_1.jpg', 'dishwasher_2.jpg', 'dishwasher_3.jpg', 7),
('Vacuum Cleaner', 'Cordless vacuum', 149.99, 18, 'vacuum_1.jpg', 'vacuum_2.jpg', 'vacuum_3.jpg', 7),
('Iron', 'Steam iron', 29.99, 40, 'iron_1.jpg', 'iron_2.jpg', 'iron_3.jpg', 7),
('Hair Dryer', 'Ionic hair dryer', 49.99, 25, 'hairdryer_1.jpg', 'hairdryer_2.jpg', 'hairdryer_3.jpg', 7),
('Shaver', 'Electric shaver', 79.99, 20, 'shaver_1.jpg', 'shaver_2.jpg', 'shaver_3.jpg', 7),
('Scale', 'Digital bathroom scale', 19.99, 50, 'scale_1.jpg', 'scale_2.jpg', 'scale_3.jpg', 7),
('Clock', 'Smart alarm clock', 39.99, 30, 'clock_1.jpg', 'clock_2.jpg', 'clock_3.jpg', 8),
('Doorbell', 'Video doorbell', 149.99, 12, 'doorbell_1.jpg', 'doorbell_2.jpg', 'doorbell_3.jpg', 8),
('Lock', 'Smart door lock', 199.99, 10, 'smartlock_1.jpg', 'smartlock_2.jpg', 'smartlock_3.jpg', 8),
('Light Strip', 'RGB LED light strip', 24.99, 55, 'lightstrip_1.jpg', 'lightstrip_2.jpg', 'lightstrip_3.jpg', 8),
('Charger', 'Wireless charger', 19.99, 60, 'wirelesscharger_1.jpg', 'wirelesscharger_2.jpg', 'wirelesscharger_3.jpg', 3),
('Case', 'Phone protective case', 14.99, 80, 'phonecase_1.jpg', 'phonecase_2.jpg', 'phonecase_3.jpg', 3),
('Screen Protector', 'Tempered glass screen protector', 9.99, 90, 'screenprotector_1.jpg', 'screenprotector_2.jpg', 'screenprotector_3.jpg', 3),
('Stylus', 'Capacitive stylus pen', 12.99, 70, 'stylus_1.jpg', 'stylus_2.jpg', 'stylus_3.jpg', 2),
('Keyboard Case', 'Tablet keyboard case', 49.99, 25, 'keyboardcase_1.jpg', 'keyboardcase_2.jpg', 'keyboardcase_3.jpg', 2),
('Stand', 'Laptop stand', 34.99, 35, 'laptopstand_1.jpg', 'laptopstand_2.jpg', 'laptopstand_3.jpg', 2),
('Cooler', 'Laptop cooling pad', 29.99, 40, 'cooler_1.jpg', 'cooler_2.jpg', 'cooler_3.jpg', 2),
('Bag', 'Laptop backpack', 59.99, 20, 'laptopbag_1.jpg', 'laptopbag_2.jpg', 'laptopbag_3.jpg', 2),
('Adapter', 'Universal adapter', 24.99, 45, 'adapter_1.jpg', 'adapter_2.jpg', 'adapter_3.jpg', 4),
('Hub', 'USB hub', 19.99, 50, 'usbhub_1.jpg', 'usbhub_2.jpg', 'usbhub_3.jpg', 4),
('Card Reader', 'SD card reader', 14.99, 60, 'cardreader_1.jpg', 'cardreader_2.jpg', 'cardreader_3.jpg', 4),
('Ethernet Cable', 'Cat6 ethernet cable', 12.99, 70, 'ethernet_1.jpg', 'ethernet_2.jpg', 'ethernet_3.jpg', 4),
('Surge Protector', 'Power strip with surge protection', 39.99, 30, 'surgeprotector_1.jpg', 'surgeprotector_2.jpg', 'surgeprotector_3.jpg', 4),
('Battery', 'AA rechargeable batteries', 14.99, 80, 'batteries_1.jpg', 'batteries_2.jpg', 'batteries_3.jpg', 4),
('Remote', 'Universal remote control', 19.99, 40, 'remote_1.jpg', 'remote_2.jpg', 'remote_3.jpg', 6),
('Streaming Device', 'Media streaming box', 49.99, 25, 'streamingdevice_1.jpg', 'streamingdevice_2.jpg', 'streamingdevice_3.jpg', 6),
('Game Controller', 'Wireless game controller', 59.99, 30, 'controller_1.jpg', 'controller_2.jpg', 'controller_3.jpg', 9),
('Headset', 'Gaming headset', 89.99, 20, 'gamingheadset_1.jpg', 'gamingheadset_2.jpg', 'gamingheadset_3.jpg', 9),
('Console Accessory', 'Controller charger', 24.99, 50, 'accessory_1.jpg', 'accessory_2.jpg', 'accessory_3.jpg', 9),
('Board Game', 'Strategy board game', 39.99, 25, 'boardgame_1.jpg', 'boardgame_2.jpg', 'boardgame_3.jpg', 9),
('Puzzle', '1000-piece puzzle', 14.99, 40, 'puzzle_1.jpg', 'puzzle_2.jpg', 'puzzle_3.jpg', 9),
('Toy Car', 'Remote control car', 29.99, 35, 'toycar_1.jpg', 'toycar_2.jpg', 'toycar_3.jpg', 9),
('Doll', 'Fashion doll', 19.99, 45, 'doll_1.jpg', 'doll_2.jpg', 'doll_3.jpg', 9),
('Building Blocks', 'Creative building set', 49.99, 20, 'blocks_1.jpg', 'blocks_2.jpg', 'blocks_3.jpg', 9),
('Art Supplies', 'Drawing and painting kit', 34.99, 30, 'artsupplies_1.jpg', 'artsupplies_2.jpg', 'artsupplies_3.jpg', 9),
('Bike', 'Kids bicycle', 149.99, 10, 'bike_1.jpg', 'bike_2.jpg', 'bike_3.jpg', 9),
('Scooter', 'Foldable kick scooter', 79.99, 15, 'scooter_1.jpg', 'scooter_2.jpg', 'scooter_3.jpg', 9),
('Ball', 'Sports ball set', 24.99, 40, 'ball_1.jpg', 'ball_2.jpg', 'ball_3.jpg', 9),
('Tent', 'Camping tent', 99.99, 12, 'tent_1.jpg', 'tent_2.jpg', 'tent_3.jpg', 9),
('Sleeping Bag', 'Compact sleeping bag', 59.99, 20, 'sleepingbag_1.jpg', 'sleepingbag_2.jpg', 'sleepingbag_3.jpg', 9),
('Backpack', 'Hiking backpack', 69.99, 18, 'backpack_1.jpg', 'backpack_2.jpg', 'backpack_3.jpg', 9),
('Flashlight', 'LED flashlight', 14.99, 60, 'flashlight_1.jpg', 'flashlight_2.jpg', 'flashlight_3.jpg', 9),
('Binoculars', 'Compact binoculars', 49.99, 25, 'binoculars_1.jpg', 'binoculars_2.jpg', 'binoculars_3.jpg', 9),
('Compass', 'Magnetic compass', 9.99, 70, 'compass_1.jpg', 'compass_2.jpg', 'compass_3.jpg', 9),
('Map', 'Topographic map', 7.99, 80, 'map_1.jpg', 'map_2.jpg', 'map_3.jpg', 9),
('First Aid Kit', 'Emergency first aid kit', 29.99, 35, 'firstaid_1.jpg', 'firstaid_2.jpg', 'firstaid_3.jpg', 9),
('Water Bottle', 'Insulated water bottle', 19.99, 50, 'waterbottle_1.jpg', 'waterbottle_2.jpg', 'waterbottle_3.jpg', 9),
('Sunglasses', 'UV protection sunglasses', 24.99, 40, 'sunglasses_1.jpg', 'sunglasses_2.jpg', 'sunglasses_3.jpg', 9),
('Hat', 'Sun hat', 14.99, 55, 'hat_1.jpg', 'hat_2.jpg', 'hat_3.jpg', 9),
('Gloves', 'Work gloves', 9.99, 65, 'gloves_1.jpg', 'gloves_2.jpg', 'gloves_3.jpg', 9),
('Tool Set', 'Basic tool set', 39.99, 25, 'toolset_1.jpg', 'toolset_2.jpg', 'toolset_3.jpg', 9),
('Paint', 'Interior paint', 29.99, 30, 'paint_1.jpg', 'paint_2.jpg', 'paint_3.jpg', 7),
('Brush', 'Paint brush set', 12.99, 50, 'brush_1.jpg', 'brush_2.jpg', 'brush_3.jpg', 7),
('Ladder', 'Step ladder', 79.99, 8, 'ladder_1.jpg', 'ladder_2.jpg', 'ladder_3.jpg', 7),
('Garden Hose', 'Expandable garden hose', 34.99, 20, 'hose_1.jpg', 'hose_2.jpg', 'hose_3.jpg', 7),
('Grill', 'Charcoal grill', 149.99, 10, 'grill_1.jpg', 'grill_2.jpg', 'grill_3.jpg', 7),
('Patio Furniture', 'Outdoor chair', 89.99, 15, 'patiochair_1.jpg', 'patiochair_2.jpg', 'patiochair_3.jpg', 7),
('Umbrella', 'Garden umbrella', 59.99, 18, 'umbrella_1.jpg', 'umbrella_2.jpg', 'umbrella_3.jpg', 7),
('Pool', 'Inflatable pool', 49.99, 12, 'pool_1.jpg', 'pool_2.jpg', 'pool_3.jpg', 7),
('Bike Lock', 'U-lock', 19.99, 40, 'bikelock_1.jpg', 'bikelock_2.jpg', 'bikelock_3.jpg', 9),
('Helmet', 'Bike helmet', 39.99, 30, 'helmet_1.jpg', 'helmet_2.jpg', 'helmet_3.jpg', 9),
('Pump', 'Bike tire pump', 14.99, 50, 'pump_1.jpg', 'pump_2.jpg', 'pump_3.jpg', 9),
('Tire', 'Bike inner tube', 9.99, 60, 'tire_1.jpg', 'tire_2.jpg', 'tire_3.jpg', 9),
('Chain', 'Bike chain', 24.99, 35, 'chain_1.jpg', 'chain_2.jpg', 'chain_3.jpg', 9),
('Pedals', 'Bike pedals', 29.99, 25, 'pedals_1.jpg', 'pedals_2.jpg', 'pedals_3.jpg', 9),
('Saddle', 'Bike saddle', 49.99, 20, 'saddle_1.jpg', 'saddle_2.jpg', 'saddle_3.jpg', 9),
('Handlebars', 'Bike handlebars', 39.99, 15, 'handlebars_1.jpg', 'handlebars_2.jpg', 'handlebars_3.jpg', 9),
('Fork', 'Bike suspension fork', 199.99, 5, 'fork_1.jpg', 'fork_2.jpg', 'fork_3.jpg', 9),
('Wheel', 'Bike wheel set', 149.99, 8, 'wheel_1.jpg', 'wheel_2.jpg', 'wheel_3.jpg', 9);

-- PROMOTIONS
INSERT INTO promotions (amount, level_limit, id_product) VALUES
(30, 1, 1);

-- SHOPPING CARTS
INSERT INTO shopping_carts (id_buyer) VALUES
(2),  -- Alice
(3);  -- Bob

-- CART ITEMS
INSERT INTO cart_items (id_shopping_cart, id_product, quantity) VALUES
(1, 3, 1),  -- Alice: Smartphone
(1, 2, 2),  -- Alice: Laptops
(2, 4, 3);  -- Bob: Graphics Cards

-- ORDERS
INSERT INTO orders (id_cart, status, date_ord) VALUES
(1, 'delivered', NOW() - INTERVAL '3 days'),
(2, 'in distribution', NOW());

-- REVIEWS
INSERT INTO reviews (id_order, id_product, title, description, rating, time_stamp) VALUES
(1, 3, 'Excellent phone', 'Battery lasts all day.', 5, NOW() - INTERVAL '2 days'),
(1, 2, 'Great laptop', 'Perfect for work and gaming.', 4, NOW() - INTERVAL '1 day');

-- REPORTS
INSERT INTO reports (id_buyer, description, status) VALUES
(2, 'Product arrived damaged', 'pending'),
(3, 'Fake listing', 'accepted');

-- REPORT_REVIEWS
INSERT INTO report_reviews (id_report, id_review) VALUES
(1, 1);

-- REPORT_PRODUCTS
INSERT INTO report_products (id_report, id_product) VALUES
(2, 9);

-- WISHLISTS
INSERT INTO wishlists (id_buyer) VALUES
(2),
(3);

-- WISHLIST_PRODUCTS
INSERT INTO wishlist_products (id_wishlist, id_product) VALUES
(1, 2),
(2, 1);

-- PRODUCT_SPECS
INSERT INTO product_specs (id_product, spec_key, spec_value) VALUES
(1, 'CPU', 'Intel Core i9-13900K'),
(1, 'GPU', 'NVIDIA RTX 4080'),
(1, 'RAM', '32GB DDR5'),
(2, 'Screen', '15.6 inch 4K'),
(2, 'Battery', '8 hours'),
(2, 'Weight', '1.5 kg');

-- NOTIFICATIONS
INSERT INTO notifications (id_buyer, title, date_not, viewed) VALUES
(2, 'Your order has been delivered!', NOW() - INTERVAL '1 day', TRUE),
(3, 'Order in distribution', NOW(), FALSE);

-- ORDER_STATUS
INSERT INTO order_status (id_notification, id_order, id_buyer) VALUES
(1, 1, 2),
(2, 2, 3);

-- PRODUCT_AVAILABLE
INSERT INTO product_available (id_notification, id_buyer, id_product) VALUES
(1, 2, 1),
(2, 3, 4);

