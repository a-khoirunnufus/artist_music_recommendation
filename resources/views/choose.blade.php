@inject('recommender', 'App\Services\Recommender')

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Bootstrap Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700&display=swap" rel="stylesheet">

    <title>Music Artist Recommendation</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Rubik', sans-serif;
            background-color: #e2d9f3;
        }
        .btn {
            font-weight: 500 !important;
        }
        .custom-container {
            width: 100%;
            max-width: 600px;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .artist-container {
            background-image: url("{{ asset('img/person.png') }}");
            background-position: center;
            object-fit: cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 300px;
            border-radius: 10px;
            justify-content: center;
        }
        .artist-container img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            border-radius: 10px;
        }
        .artist-container h1 {
            /*margin-top: -2rem;*/
            background-color: #6f42c1;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            color: white;
            display: inline-block;
            z-index: 2;
        }
        .artist-profile-link {
            background-color: white;
            padding: .5rem 1rem;
            border-radius: 10px;
        }
        .btn-group {
            width: 100%;
        }
        .btn-group .btn {
            width: 50%;
        }

        /*side menu*/
        .side-container {
            position: fixed;
            top: 0;
            right: -300px;
            bottom: 0;
            width: 100%;
            max-width: 300px;
            z-index: 10;
            background-color: #160d27;
            /*border-radius: 10px 0 0 10px;*/
            transition: right .3s;
            padding-top: 6rem;
        }
        .side-container.show {
            right: 0;
        }
        .side-menu-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #side-toggle-btn {
            background-color: transparent;
            padding: 0;
            border: none;
            outline: none;
            cursor: pointer;
        }
        #side-toggle-btn i {
            color: #1a202c;
            font-size: 3rem;
            margin-left: -2.5rem;
            background-color: white;
            border-radius: 10px;
            position: absolute;
            top: 1rem;
            padding: 0 1rem;
        }
    </style>
  </head>
  <body>
    <div class="custom-container">
        <h2 class="fw-bolder text-center mb-5">Do you like to listen to this artist?</h2>
        <div class="artist-container mb-5">
{{--            <img src="{{ asset('img/person.png') }}" alt="...">--}}
            <h1 class="text-center">{{ $artist->name }}</h1>
            <h6 class="artist-profile-link mt-3">
                <a id="artist-profile-url" href="" target="_blank" class="text-decoration-underline">
                    <i class="bi bi-box-arrow-up-right"></i> Open Artist Profile
                </a>
            </h6>
        </div>
        <form id="engagement-form" action="/engagement" method="post">
            @csrf
            <input value="{{ $artist->_id }}" type="hidden" name="artist_id">
            <input value="" type="hidden" name="engagement_type">
        </form>
        <div class="btn-group" role="group">
            <a class="btn-engagement btn btn-danger btn-lg text-white" data-type="unlike">No, I don't</a>
            <a class="btn-engagement btn btn-secondary btn-lg text-white" data-type="skip">Skip this</a>
            <a class="btn-engagement btn btn-success btn-lg text-white" data-type="like">Yes, I do</a>
        </div>
        @if($recommender->getNumOfLikes() + $recommender->getNumOfUnlikes() >= 5)
            <hr class="my-5" />
            <h5 class="mt-3 text-center">You can get the recommendation now</h5>
            <button
                onclick="
                    alert('Please wait, it will take about 1 minute.');
                    window.location.href = '/recommendation';
                "
                class="btn btn-primary btn-lg mt-3" style="width: 100%">Get Recommendation</button>
        @endif
    </div>

    <div class="side-container">
        <button id="side-toggle-btn"><i class="bi bi-list"></i></button>
        <div class="side-menu-container">
            <h1 class="text-white"><i class="bi bi-hand-thumbs-up-fill"></i> {{ $recommender->getNumOfLikes() }}</h1>
            <h5 class="text-white mb-5">#LikedArtist</h5>
            <h1 class="text-white"><i class="bi bi-hand-thumbs-down-fill"></i> {{ $recommender->getNumOfUnlikes() }}</h1>
            <h5 class="text-white mb-5">#UnlikedArtist</h5>
            <button
                onclick="
                    alert('Please wait, it will take about 1 minute.');
                    window.location.href = '/recommendation';
                "
                class="btn btn-white btn-lg mb-3"
                style="display: inline-block">Get Recommendation</button>
            <button
                onclick="window.location.href='/reset-preferences'"
                class="btn btn-white btn-lg"
                style="display: inline-block">Reset Preferences</button>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script>
      $( document ).ready(function() {
          const artistName = "{{ $artist->name }}";

          fetch('https://ws.audioscrobbler.com/2.0/' +
                    '?method=artist.getinfo' +
                    `&artist=${artistName}&limit=1` +
                    '&api_key=45fabda6bcdb1634e78b784a9b1ef735' +
                    '&format=json')
              .then(response => response.json())
              .then(data => {
                  const artistUrl = data.artist.url;
                  $('#artist-profile-url').attr('href', artistUrl);
              });

          $('#side-toggle-btn').click(function () {
             $('.side-container').toggleClass('show');
          });

          $('.btn-engagement').click(function () {
              const engagementType = $(this).attr('data-type');
              $("#engagement-form > input[name='engagement_type']").val(engagementType);
              $("#engagement-form").submit();
          });
      });
  </script>
  </body>
</html>
