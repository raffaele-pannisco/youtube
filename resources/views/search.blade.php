<header>
    <h1>The Video Gallery</h1>
</header>
@foreach($store as $data)
<article class="video">
    <figure>
        <a class="fancybox fancybox.iframe" href="http://www.youtube.com/embed/{{ $data['id'] }}">
            <img class="videoThumb" src="{{ $data['img'] }}"></a>
    </figure>
    <h2 class="videoTitle">{{ $data['title'] }}</h2>
</article>
@endforeach

