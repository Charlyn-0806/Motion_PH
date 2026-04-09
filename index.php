<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parc Divas Dance System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    :root {
        --parc-green: #2e7d32; 
        --parc-lime: #8bc34a;  
        --parc-white: #ffffff;
        --parc-dark: #121212;
    }

    body { 
        font-family: 'Nunito', sans-serif; 
        background-color: var(--parc-dark);
        color: var(--parc-white);
    }
    
    .hero-section {
        background: linear-gradient(rgba(18, 18, 18, 0.6), rgba(46, 125, 50, 0.4)), 
                    url('images/background.jpg') no-repeat center center;
        background-size: cover;
        height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .navbar { 
        background: var(--parc-white); 
        border-bottom: 4px solid var(--parc-green); 
    }

    .navbar-brand { 
        color: var(--parc-green) !important; 
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    .btn-primary { 
        background-color: var(--parc-green); 
        border: none; 
        color: white;
        padding: 12px 30px;
        transition: 0.3s;
    }

    .btn-primary:hover { 
        background-color: var(--parc-lime);
        color: #121212;
        transform: translateY(-2px);
    }

    .btn-outline-green {
        border: 2px solid var(--parc-green);
        color: var(--parc-green);
        font-weight: 700;
    }

    .btn-outline-green:hover {
        background-color: var(--parc-green);
        color: white;
    }

    /* Modal Styling Fixes */
    .modal-content {
        background-color: var(--parc-white);
        color: var(--parc-dark);
        border-radius: 15px;
    }
    .form-control:focus {
        border-color: var(--parc-green);
        box-shadow: 0 0 0 0.25 cold rgba(46, 125, 50, 0.25);
    }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="#">
            <span style="color: #121212;">PARC</span> DIVAS
        </a>
        <div class="ms-auto">
            <button class="btn btn-outline-green me-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#loginModal">Member Login</button>
            <button class="btn btn-primary rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#signupModal">Sign Up</button>
        </div>
    </div>
</nav>

<header class="hero-section">
    <div class="container">
        <h1 class="display-2 fw-bold mb-3">Parc Divas Dance System</h1>
        <p class="lead fs-3 mb-4">Promoting physical fitness through the art of dance for every age at Parc Regency Residences.</p>
        <a href="#purpose" class="btn btn-primary btn-lg rounded-pill shadow">Discover Our Purpose</a>
    </div>
</header>

<section id="purpose" class="purpose-section py-5">
    <div class="container text-center">
        <h2 class="section-title mb-5">Our Purpose</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="purpose-card card-1 shadow p-4 feature-card h-100">
                    <i class="fas fa-video mb-3 fa-2x text-lime"></i>
                    <h5>Interactive 3D Guidance</h5>
                    <p>Experience precision with AI-driven avatars that ensure your form is perfect regardless of skill level.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="purpose-card card-2 shadow p-4 feature-card h-100">
                    <i class="fas fa-brain mb-3 fa-2x text-lime"></i>
                    <h5>Smart Choreography</h5>
                    <p>Get real-time, AI-generated textual breakdowns of complex routines to help you memorize faster.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="purpose-card card-3 shadow p-4 feature-card h-100">
                    <i class="fas fa-heartbeat mb-3 fa-2x text-lime"></i>
                    <h5>Seamless Monitoring</h5>
                    <p>Automatically track your steps and calories burned while monitoring your dance journey over time.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h4 class="modal-title fw-bold text-success" id="loginModalLabel">Member Login</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <?php if(isset($_GET['signup']) && $_GET['signup'] == 'success'): ?>
            <div class="alert alert-success p-2 small">Account created! You can now login.</div>
        <?php endif; ?>

        <?php if(isset($_GET['error']) && $_GET['error'] == 'exists'): ?>
            <div class="alert alert-danger p-2 small">Username or Email already taken!</div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill mt-2">Sign In</button>
        </form>
        <div class="text-center mt-3 small">
            New here? <a href="#" data-bs-toggle="modal" data-bs-target="#signupModal" data-bs-dismiss="modal" class="text-success fw-bold text-decoration-none">Create an Account</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h4 class="modal-title fw-bold text-success" id="signupModalLabel">Join Parc Divas</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form action="signup_process.php" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Age Group</label>
                <select name="age_group" class="form-select" required>
                    <option value="" disabled selected>Select your age group</option>
                    <option value="Child">Child (Below 18)</option>
                    <option value="Adult">Adult (18 - 59)</option>
                    <option value="Senior">Senior (60+)</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill mt-2">Register Now</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>