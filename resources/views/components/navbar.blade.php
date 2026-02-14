
 <nav class="navbar navbar-expand-lg ftco-navbar-dark sticky-top"
     style="
    backdrop-filter: blur(10px);             /* Efek blur */
    -webkit-backdrop-filter: blur(10px);     /* Untuk Safari */
    background-color: rgba(255, 255, 255, 0.8); /* Putih semi transparan */
    top: 0;
    z-index: 1000;
  ">

     <!-- Container medium, dengan padding horizontal dan vertikal nyaman -->
     <div class="container px-4 py-3 d-flex justify-content-between align-items-center">

         <!-- Judul Website -->
         <a class="navbar-brand text-dark font-weight-bold" href="#" style="font-size: 1.5rem;">
             PesonaBali
         </a>

         <!-- Tombol Toggle Mobile -->
         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav"
             aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
             <span class="oi oi-menu"></span> Menu
         </button>

         <!-- Navigasi dan Search -->
         <div class="collapse navbar-collapse justify-content-end" id="ftco-nav">

             <!-- Menu Navigasi -->
             <ul class="navbar-nav mr-3">
                 <li class="nav-item active"><a href="{{ url('/') }}" class="nav-link text-dark"
                         style="font-size: 1.2rem;">Home</a></li>
                 <li class="nav-item active"><a href="{{ url('/destinations') }}" class="nav-link text-dark"
                         style="font-size: 1.2rem;">Destination</a></li>
                 <li class="nav-item active"><a href="{{ url('/about') }}" class="nav-link text-dark"
                         style="font-size: 1.2rem;">About</a></li>
                 <li class="nav-item active"><a href="{{ url('/contact') }}" class="nav-link text-dark"
                         style="font-size: 1.2rem;">Contact</a></li>
             </ul>

         </div>
     </div>
 </nav>
