<footer class="text-white pt-5 pb-4" style="background-color: #192C57; border-top: 5px solid #CEAA0C;">
    <div class="container text-center text-md-start">
        <div class="row text-center text-md-start">
            
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold" style="color: #CEAA0C;">Peaks Hotel Cafeteria</h5>
                <p>
                    Providing quality, affordable, and nutritious meals to the KCA University community. 
                    Served with dignity and excellence.
                </p>
            </div>

<div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold" style="color: #CEAA0C;">Quick Links</h5>
                <p><a href="{{ route('home') }}" class="text-white" style="text-decoration: none;">Home</a></p>
                <p><a href="{{ route('menu.all') }}" class="text-white" style="text-decoration: none;">Full Menu</a></p>
                @auth
                    <p><a href="{{ route('orders.index') }}" class="text-white" style="text-decoration: none;">My Orders</a></p>
                @else
                    <p><a href="{{ route('login') }}" class="text-white" style="text-decoration: none;">Staff Portal</a></p>
                @endauth
            </div>

            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold" style="color: #CEAA0C;">Contact</h5>
                <p><i class="fas fa-home mr-3"></i> KCA University, Main Campus</p>
                <p><i class="fas fa-envelope mr-3"></i> catering@kcau.ac.ke</p>
                <p><i class="fas fa-phone mr-3"></i> +254 700 123 456</p>
            </div>
            
        </div>

        <hr class="mb-4">

        <div class="row align-items-center">
            <div class="col-md-7 col-lg-8">
                <p> © {{ date('Y') }} <strong>KCA University</strong>. All Rights Reserved.
                </p>
            </div>
            <div class="col-md-5 col-lg-4">
                <div class="text-center text-md-right">
                    <ul class="list-unstyled list-inline">
                        <li class="list-inline-item">
                            <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="fab fa-facebook"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="fab fa-twitter"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" class="btn-floating btn-sm text-white" style="font-size: 23px;"><i class="fab fa-instagram"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>