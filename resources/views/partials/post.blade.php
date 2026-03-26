<article class="post" id="post{{ $id }}">
    <h3>Post {{ $id }}</h3>
    <img src="/post/{{ $id }}.jpg">
    <button class="not-clicked" onclick="like({{ $id }})">Like!</button>
</article>
