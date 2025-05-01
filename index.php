<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow My Charger - Find EV Charging Points</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #0077B6;
            --secondary-green: #00CC66;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            padding-top: 90px; /* Offset for fixed navbar */
        }
        
        /* Enhanced Navbar Styling */
        .navbar {
            min-height: 90px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            padding: 0;
            margin-right: 2rem;
        }
        
        .navbar-brand img {
            height: 75px;
            transition: transform 0.3s;
        }
        
        .navbar-brand:hover img {
            transform: scale(1.05);
        }
        
        /* Navigation Links */
        .navbar-nav .nav-item {
            display: flex;
            align-items: center;
        }
        
        .navbar-nav .nav-link {
            font-size: 1.15rem;
            font-weight: 500;
            padding: 1.1rem 1.5rem;
            color: var(--dark-gray);
            transition: all 0.3s;
            position: relative;
        }
        
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary-blue);
        }
        
        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1.5rem;
            right: 1.5rem;
            height: 3px;
            background: var(--primary-blue);
        }
        
        /* Navigation Buttons */
        .navbar .btn {
            font-size: 1.1rem;
            font-weight: 500;
            padding: 0.8rem 1.75rem;
            margin-left: 0.75rem;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .navbar .btn-outline-primary {
            border-width: 2px;
        }
        
        .navbar .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Mobile Menu Adjustments */
        @media (max-width: 991.98px) {
            body {
                padding-top: 0;
            }
            
            .navbar {
                min-height: auto;
                padding: 1rem 0;
            }
            
            .navbar-brand img {
                height: 60px;
            }
            
            .navbar-nav {
                padding: 1rem 0;
            }
            
            .navbar-nav .nav-link {
                padding: 0.8rem 1rem;
                font-size: 1.1rem;
            }
            
            .navbar-nav .nav-link.active::after {
                left: 1rem;
                right: 1rem;
            }
            
            .navbar .btn {
                margin: 0.75rem 0 0;
                width: 100%;
            }
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1606220838315-056192d5e927?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
        }
        
        .search-box {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Features Section */
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 20px;
        }
        
        .feature-card {
            padding: 25px;
            border-radius: 10px;
            transition: all 0.3s;
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-blue);
        }
        
        /* Buttons */
        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .btn-outline-primary {
            color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-blue);
            color: white;
        }
        
        /* Footer */
        footer {
            background-color: var(--dark-gray);
            color: white;
            padding: 50px 0 20px;
            margin-top: 50px;
        }
        
        .social-icons a {
            color: white;
            font-size: 1.2rem;
            margin-right: 15px;
            transition: all 0.3s;
        }
        
        .social-icons a:hover {
            color: var(--secondary-green);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
               <img src="images/BMC_LOGO.png" alt="Borrow My Charger Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">How It Works</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="login.html" class="btn btn-outline-primary me-2">Login</a>
                    <a href="register.html" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-4">Find EV Charging Points Near You</h1>
                    <p class="lead mb-5">Connect with local homeowners to borrow their charging points when you need them</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="search-box">
                        <form id="searchForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="location" class="form-label">Location</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" class="form-control" id="location" placeholder="Enter postcode or address">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="priceRange" class="form-label">Max Price (kWh)</label>
                                    <select class="form-select" id="priceRange">
                                        <option value="">Any price</option>
                                        <option value="0.15">£0.15</option>
                                        <option value="0.20">£0.20</option>
                                        <option value="0.25">£0.25</option>
                                        <option value="0.30">£0.30</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="availability" class="form-label">Availability</label>
                                    <select class="form-select" id="availability">
                                        <option value="">Now</option>
                                        <option value="today">Today</option>
                                        <option value="tomorrow">Tomorrow</option>
                                        <option value="weekend">This Weekend</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-search me-2"></i>Search Chargers
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container mb-5">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold">How It Works</h2>
                <p class="lead text-muted">Simple steps to find or share a charging point</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h4>Find Chargers Nearby</h4>
                    <p class="text-muted">Search our network of home charging points by location, price and availability.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-pound-sign"></i>
                    </div>
                    <h4>Earn From Your Charger</h4>
                    <p class="text-muted">Homeowners can list their charging points and earn money when others use them.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h4>Easy Booking System</h4>
                    <p class="text-muted">Simple calendar booking system with instant confirmation and secure payments.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Preview Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="fw-bold mb-4">Interactive Map</h2>
                    <p class="lead">Find available charging points near your location with our real-time map.</p>
                    <p>Our interactive map shows all available charging points in your area with detailed information about each location.</p>
                    <a href="#" class="btn btn-outline-primary">Explore Map</a>
                </div>
                <div class="col-lg-6">
                    <div class="ratio ratio-16x9">
                        <img src="https://maps.googleapis.com/maps/api/staticmap?center=53.483710,-2.270110&zoom=13&size=600x300&maptype=roadmap&markers=color:blue%7C53.483710,-2.270110&key=YOUR_API_KEY" alt="Map Preview" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5">
        <div class="container text-center">
            <h2 class="fw-bold mb-4">Ready to get started?</h2>
            <p class="lead mb-5">Join our growing community of EV owners and homeowners today.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="register.html?role=user" class="btn btn-primary btn-lg px-4">Find a Charger</a>
                <a href="register.html?role=homeowner" class="btn btn-outline-primary btn-lg px-4">List Your Charger</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>Borrow My Charger</h5>
                    <p>Connecting EV owners with home charging points since 2023.</p>
                    <div class="social-icons">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Home</a></li>
                        <li><a href="#" class="text-white">About</a></li>
                        <li><a href="#" class="text-white">How It Works</a></li>
                        <li><a href="#" class="text-white">Pricing</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Legal</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Privacy Policy</a></li>
                        <li><a href="#" class="text-white">Terms of Service</a></li>
                        <li><a href="#" class="text-white">Cookie Policy</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> info@borrowmycharger.com</li>
                        <li><i class="fas fa-phone me-2"></i> +44 123 456 7890</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i> Salford, UK</li>
                    </ul>
                </div>
            </div>
            <hr class="mt-4 mb-3">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="small mb-0">&copy; 2023 Borrow My Charger. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="small mb-0">Designed for IT8415 Database Programming 2</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // This would be replaced with your actual search functionality
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // AJAX call to search functionality would go here
            console.log('Search submitted');
            // For demonstration, just show an alert
            alert('Search functionality would be implemented here with AJAX calls to your PHP backend');
        });
    </script>
</body>
</html>