<!doctype html>
<html lang="en">
   <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <link rel="stylesheet" href="asset/css/mapbox-gl.css" />
      <link rel="stylesheet" href="asset/css/libs.bundle.css" />
      <link rel="stylesheet" href="asset/css/theme.bundle.css" />
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
      <title>SIPANTAU WNA | sistem pemantauan warga negara asing</title>
      <style type="text/css">
         h1,h2,h3{
         font-family: 'Poppins', sans-serif !important;
         font-weight: bolder !important;
         }
      </style>
   </head>
   <body style="font-family: 'Poppins', sans-serif !important; ">
      <section class="pt-8 pt-md-11 bg-gradient-light-white">
         <div class="container">
            <div class="row align-items-center justify-content-between mb-8 mb-md-11">
               <div class="col-12 col-md-6 order-md-2" data-aos="fade-left">
                  <h2>
                     SIPANTAU WNA<br>
                     <span class="text-success">
                        <h5>sistem pemantauan warga negara asing</h5>
                     </span>
                  </h2>
                  <img src="asset/img/illustrations/illustration-2.png" class="img-fluid mw-md-150 mw-lg-100 mb-6 mb-md-0" alt="..." data-aos="fade-up" data-aos-delay="100">
               </div>
               <div class="col-12 col-md-6 col-lg-5 order-md-1" data-aos="fade-right">
                  <!-- Card -->
                  <div class="card shadow-light-lg lift lift-lg">
                     <!-- Image -->
                     <!-- Shape -->
                     <div class="position-relative">
                        <div class="shape shape-bottom shape-fluid-x text-white">
                           <svg viewBox="0 0 2880 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path d="M0 48h2880V0h-720C1442.5 52 720 0 720 0H0v48z" fill="currentColor"/>
                           </svg>
                        </div>
                     </div>
                     <!-- Body -->
                     <div class="card-body">
                        <!-- Form -->
                        <form action="index.php?p=login" method="post">
                           <div class="form-floating">
                              <input type="text" class="form-control form-control-flush" id="userdName" placeholder="Name" name="userName">
                              <label for="cardName">username</label>
                           </div>
                           <div class="form-floating">
                              <input type="password" class="form-control form-control-flush" id="Password" placeholder="Password" name="passWord">
                              <label for="cardPassword">password</label>
                           </div>
                           <div class="mt-6">
                              <button class="btn w-100 btn-success lift" type="submit" name="logMeIn">
                              Login
                              </button>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
            <!-- / .row -->
         </div>
         <!-- / .container -->
      </section>
      <!-- JAVASCRIPT -->
      <!-- Map JS -->
      <script src='asset/js/mapbox-gl.js'></script>
      <!-- Vendor JS -->
      <script src="asset/js/vendor.bundle.js"></script>
      <!-- Theme JS -->
      <script src="asset/js/theme.bundle.js"></script>
   </body>
</html>

