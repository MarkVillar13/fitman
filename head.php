<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<head>
  <meta charset="UTF-8">
  <title>FitMan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/x-icon" href="assets\img\158534286_1325351574499404_570873476507921606_n.jpg">
  <link rel="stylesheet" href="scripts/w3.css">
  <link rel="stylesheet" href="scripts/bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <script src="scripts/bootstrap-5.3.3-dist/js/bootstrap.min.js"></script>
  <script src="scripts/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
  <script src="scripts/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-deep-purple.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
  </script>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
  <script src="scripts/instascan.min.js"></script>
  <script src="https://www.gstatic.com/charts/loader.js"></script>
  <script>
    google.charts.load('current', {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawChart);
  </script>
  <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>
<style media="screen">
body{

}
a{
  text-decoration: none;
}
.icons {
filter: brightness(0) invert(1);
transition: filter 0.3s ease;
}
.icons:hover {
filter: brightness(0) invert(0);
}
#page {
      overflow: auto;
      max-height: 90vh;
      padding: 10px;
      padding-bottom: 32px;
  }
#page::-webkit-scrollbar {
      display: none;
  }
  body::-webkit-scrollbar {
        display: none;
    }
  #offer {
        overflow: auto;
        max-height: 80vh;
        padding: 10px;
        padding-bottom: 32px;
    }
    #offer::-webkit-scrollbar {
              display: none;
          }
  p {
    text-indent: 20px;
    text-align: justify;
  }
</style>
<style media="print">
  p {
    text-indent: 20px;
    text-align: justify;
  }
</style>
<style>
#calendar-header {
  text-align: center;
  margin-bottom: 10px;
  color: black;
}

.today {
  background-color: skyblue;
  color: blue;
}
</style>
<style>
    .item-image {
        background: black;
        border: 2px solid #ccc;
        border-radius: 5px;
        padding: 5px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .item-image:hover {
        transform: scale(1.15);
        box-shadow: 0 8px 15px rgba(0, 0, 0, .8);
    }
    .offers-image {
        background: black;
        border: 2px solid #ccc;
        border-radius: 5px;
        padding: 5px;
        height: 50vh
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .offers-image:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 15px rgba(0, 0, 0, .8);
    }
    .carousel-image {
        padding: 5px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .carousel-image:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 15px rgba(0, 0, 0, .8);
    }
    .products .box-container{
        display: flex;
        flex-wrap: wrap;
        gap:1.5rem;
    }

    .products .box-container .box{
        flex:1 1 30rem;
        box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.1);
        border-radius: .5rem;
        border:.1rem solid rgba(0,0,0,.1);
        position: relative;
    }

    .products .box-container .box .discount{
        position: absolute;
        top:1rem; left:1rem;
        padding:.7rem 1rem;
        font-size: 2rem;
        color:var(--pink);
        background:rgba(255, 51, 153,.05);
        z-index: 1;
        border-radius: .5rem;
    }

    .products .box-container .box .image{
        position: relative;
        text-align: center;
        padding-top: 2rem;
        overflow:hidden;
    }

    .products .box-container .box .image img{
        height:25rem;
    }

    .products .box-container .box:hover .image img{
        transform: scale(1.1);
    }

    .products .box-container .box .image .icons{
        position: absolute;
        bottom:-7rem; left:0; right:0;
        display: flex;
    }

    .products .box-container .box:hover .image .icons{
        bottom:0;
    }

    .products .box-container .box .image .icons a{
        height: 5rem;
        line-height: 5rem;
        font-size: 2rem;
        width:50%;
        background:var(--pink);
        color:#fff;
    }

    .products .box-container .box .image .icons .cart-btn{
        border-left: .1rem solid #fff7;
        border-right: .1rem solid #fff7;
        width:100%;
    }

    .products .box-container .box .image .icons a:hover{
        background:#333;
    }

    .products .box-container .box .content{
        padding:2rem;
        text-align: center;
    }

    .products .box-container .box .content h3{
        font-size: 2.5rem;
        color:#333;
    }

    .products .box-container .box .content .price{
        font-size: 2.5rem;
        color:var(--pink);
        font-weight: bolder;
        padding-top: 1rem;
    }

    .products .box-container .box .content .price span{
        font-size: 1.5rem;
        color:#999;
        font-weight: lighter;
        text-decoration: line-through;
    }
</style>
