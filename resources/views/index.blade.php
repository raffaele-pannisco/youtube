
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="author" href="//plus.google.com/u/0/+AngelaHoldenDesign/posts">
        <meta name="description" content="Learn to build a simple, responsive grid for displaying and playing videos using CSS and jQuery.">
        <link rel="shortcut icon" href="favicon.ico">
        <title>Responsive Video Gallery</title>
        <script
            src="http://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
        <link href='//fonts.googleapis.com/css?family=Noto+Sans:400,700' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="{{ asset('css/app.css')}}">
        <!--[if lt IE 9]>
        <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>
    <body>
        <section class="first">
            <header>
                <h1>Search Youtube Videos</h1>
            </header>
            <p><input type="text" class="form-controller" id="search" name="search" value=""></p>



        </section>
        <section class="second clearfix">
            <div id="ajaxbody">

            </div>
        </section>


        <script
            src="http://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>

        <script src="{{ asset('js/app.js')}}"></script>

        <script>
            $(document).ready(function () {
                $('#search').val("");
                $('.fancybox').fancybox({
                    padding: 0,
                    maxWidth: '100%',
                    maxHeight: '100%',
                    width: 560,
                    height: 315,
                    autoSize: true,
                    closeClick: true,
                    openEffect: 'elastic',
                    closeEffect: 'elastic'
                });
            });

            const search = document.getElementById('search');
            const ajaxBody = document.getElementById('ajaxbody');
            function getContent() {

                const searchValue = search.value;

                const xhr = new XMLHttpRequest();
                xhr.open('GET', '{{route('search')}}/?search=' + searchValue, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.onreadystatechange = function () {

                    if (xhr.readyState == 4 && xhr.status == 200)
                    {
                        ajaxBody.innerHTML = xhr.responseText;
                        
                    }
                }
                xhr.send();
            }
            search.addEventListener('input', getContent);
        </script>
        <footer>
            <p>Responsive Video Gallery Test Project on Laravel 7<p>
            <p>Enjoy</p>
            <a href="#" class="scroll-top">↑</a>
        </footer>
    </body>
</html>