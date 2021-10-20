<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
      @font-face {
        font-family: 'ArberVintage';
        src: url("file://{{ resource_path('assets/fonts/Arber-Vintage-Free.ttf') }}") format('truetype');
      }

      @font-face {
        font-family: 'ForumRegular';
        src: url("file://{{ resource_path('assets/fonts/Forum-Regular.ttf') }}") format('truetype');
      }

      html,
      body {
        height: 100%;
      }

      body {
        background: -webkit-linear-gradient(90deg, #89a3ab, #859fa9, #809ba7, #7c98a5, #7994a3, #7590a0, #728c9e, #6f889c);
        width: 1072px;
        height: 1072px;
        line-height: 1072px;
        text-align: center;
      }

      .quote {
        color: #faf8f5;
        max-width: 760px;
        margin: 0 auto;
        display: inline-block;
        vertical-align: middle;
      }

      .quote__content {
        font-family: 'ArberVintage';
        text-align: center;
        font-size: 48px;
        line-height: 1.2;
        font-weight: 100;
      }

      .quote__book,
      .quote__author {
        font-family: 'ForumRegular';
        margin-top: 40px;
        font-size: 24px;
        line-height: 1.4;
        font-weight: 400;
        text-transform: uppercase;
        text-align: center;
        letter-spacing: .8rem;
      }
    </style>
</head>
<body>
<div class="quote">
    <div class="quote__content">{{ $text }} </div>
    <div class="quote__book">{{ $book['author']['name'] }} </div>
    <div class="quote__author">{{ $book['title'] }} </div>
</div>
</body>
</html>