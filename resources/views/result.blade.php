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
              margin: 2rem auto;
          }
          .custom-container--center {
              width: 100%;
              max-width: 600px;
              position: fixed;
              top: 50%;
              left: 50%;
              transform: translate(-50%, -50%);
          }
          .artist-container {
              display: flex;
              flex-direction: column;
              align-items: center;
          }
          .artist-container img {
              width: 100%;
              height: 300px;
              object-fit: cover;
              border-radius: 10px;
          }
          .artist-container h1 {
              margin-top: -3rem;
              background-color: #6f42c1;
              padding: 0.5rem 1rem;
              border-radius: 10px;
              color: white;
              display: inline-block;
              z-index: 2;
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

          .recommendation-list {
              display: grid;
              grid-template-columns: 1fr 1fr;
              grid-gap: 2rem;
          }
          .recommendation-list .list-item {
              border: 1px solid grey;
              display: flex;
              flex-direction: column;
              align-items: center;
              position: relative;
              background-color: white;
              border-radius: 10px;
          }
          .recommendation-list .list-item img {
              width: 100%;
              height: 200px;
              object-fit: cover;
              border-radius: 10px;
          }
          .recommendation-list .list-item .list-item-number {
              background-color: #fd7e14;
              color: white;
              width: 3rem;
              line-height: 3rem;
              text-align: center;
              vertical-align: center;
              border-radius: 1.5rem;
              position: absolute;
              top: -1.5rem;
              left: -1.5rem;
          }
          .recommendation-list .list-item .list-item-name {
              background-color: #6f42c1;
              color: white;
              width: fit-content;
              padding: .5rem 1rem;
              margin-top: -2rem;
              display: inline-block;
              z-index: 2;
              border-radius: 10px;
          }
      </style>
  </head>
  <body>
    <div class="custom-container">
        <h2>Recommendation List</h2>
        <p class="mb-5">Here the list of artist we recommend for you.</p>
        <div class="recommendation-list">
            @foreach($recommendations as $idx => $recommendation)
                <div class="list-item">
                    <h3 class="list-item-number">#{{ $idx+1 }}</h3>
                    <img src="{{ asset('img/person.png') }}" alt="...">
                    <h3 class="list-item-name text-center">{{ $recommendation['artist']['name'] }}</h3>

                    <h6 class="artist-profile-link" style="margin: .75rem">
                        <a href="https://www.last.fm/music/{{ str_replace(' ', '+', $recommendation['artist']['name']) }}" target="_blank" class="text-decoration-underline">
                            <i class="bi bi-box-arrow-up-right"></i> Listen Artist Music
                        </a>
                    </h6>

                    <h5>Probability: {{ $recommendation['probability'] }}%</h5>
                    <p class="mb-1">
                        Country: {{ implode(", ",$recommendation['artist']['country']) }}
                    </p>
                    <p class="mb-4">Num of Listeners: {{ $recommendation['artist']['listeners'] }}</p>
                </div>
            @endforeach
        </div>
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
        $('#side-toggle-btn').click(function () {
            $('.side-container').toggleClass('show');
        });
    </script>
  </body>
</html>
