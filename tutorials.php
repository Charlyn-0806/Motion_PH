<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

$user_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php?error=session_expired");
    exit();
}

$stmt = $pdo->query("SELECT * FROM dance_steps");
$tutorials = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AI Zumba Coach - Parc Divas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.ico">
    <style>
       
       /* ─── AVATAR STAGE ─── */
        .avatar-stage {
            width: 100%;
            height: 420px;
            background: radial-gradient(ellipse at 50% 80%, #1b3022 0%, #0a1a0e 100%);
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Dance floor grid lines */
        .avatar-stage::before {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 120px;
            background: repeating-linear-gradient(
                90deg,
                transparent, transparent 60px,
                rgba(46,125,50,0.15) 60px, rgba(46,125,50,0.15) 61px
            ),
            repeating-linear-gradient(
                0deg,
                transparent, transparent 30px,
                rgba(46,125,50,0.15) 30px, rgba(46,125,50,0.15) 31px
            );
        }

        /* Spotlight */
        .avatar-stage::after {
            content: '';
            position: absolute;
            top: -60px; left: 50%;
            transform: translateX(-50%);
            width: 300px;
            height: 500px;
            background: radial-gradient(ellipse, rgba(139,195,74,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ═══ DISCO ANIMATIONS ═══ */
        @keyframes discoPulse1 {
            0%, 100% { opacity: 0.1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.2); }
        }
        @keyframes discoPulse2 {
            0%, 100% { opacity: 0.15; transform: scale(1.1); }
            50% { opacity: 0.7; transform: scale(0.9); }
        }
        @keyframes discoRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes discoFlash {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }

        .disco-light {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(20px);
        }
        .disco-light-1 {
            width: 200px; height: 200px;
            top: -40px; left: -40px;
            background: radial-gradient(circle, rgba(255, 0, 255, 0.6) 0%, transparent 70%);
            animation: discoPulse1 1.2s ease-in-out infinite;
        }
        .disco-light-2 {
            width: 180px; height: 180px;
            top: -20px; right: -30px;
            background: radial-gradient(circle, rgba(0, 255, 255, 0.5) 0%, transparent 70%);
            animation: discoPulse2 1.5s ease-in-out infinite 0.3s;
        }
        .disco-light-3 {
            width: 160px; height: 160px;
            bottom: 40px; left: -20px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.5) 0%, transparent 70%);
            animation: discoPulse1 1.3s ease-in-out infinite 0.6s;
        }
        .disco-light-4 {
            width: 170px; height: 170px;
            bottom: 50px; right: -40px;
            background: radial-gradient(circle, rgba(0, 255, 100, 0.5) 0%, transparent 70%);
            animation: discoPulse2 1.4s ease-in-out infinite 0.9s;
        }
        .disco-light-5 {
            width: 150px; height: 150px;
            top: 50%; left: 10%;
            background: radial-gradient(circle, rgba(255, 100, 0, 0.5) 0%, transparent 70%);
            animation: discoPulse1 1.1s ease-in-out infinite 1.2s;
        }
        .disco-light-6 {
            width: 140px; height: 140px;
            top: 40%; right: 15%;
            background: radial-gradient(circle, rgba(255, 0, 100, 0.5) 0%, transparent 70%);
            animation: discoPulse2 1.6s ease-in-out infinite 0.45s;
        }

        /* Rotating mirror ball effect */
        .disco-mirror {
            position: absolute;
            width: 120px;
            height: 120px;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.8), rgba(100,150,255,0.4));
            border-radius: 50%;
            animation: discoRotate 8s linear infinite;
            filter: blur(3px);
            opacity: 0.4;
            pointer-events: none;
            box-shadow: 
                0 0 30px rgba(0, 200, 255, 0.5),
                inset -10px -10px 30px rgba(0, 0, 0, 0.3);
        }

        /* Strobe flash effect */
        .disco-strobe {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: rgba(255, 255, 255, 0);
            pointer-events: none;
            animation: discoFlash 0.15s ease-in-out 8;
            animation-iteration-count: infinite;
            animation-delay: 2s;
        }

        /* ─── SVG AVATAR ANIMATIONS ─── */
        #avatarSvg {
            filter: drop-shadow(0 8px 20px rgba(0,0,0,0.6));
            transition: transform 0.3s ease;
        }

        /* IDLE - gentle sway */
        @keyframes idle-body {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-4px) rotate(1deg); }
        }
        @keyframes idle-larm { 0%, 100% { transform: rotate(20deg); } 50% { transform: rotate(30deg); } }
        @keyframes idle-rarm { 0%, 100% { transform: rotate(-20deg); } 50% { transform: rotate(-30deg); } }

        /* MARCH - legs pumping */
        @keyframes march-lleg { 0%, 100% { transform: rotate(0deg); } 50% { transform: rotate(-40deg); } }
        @keyframes march-rleg { 0%, 100% { transform: rotate(-40deg); } 50% { transform: rotate(0deg); } }
        @keyframes march-body { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-6px); } }
        @keyframes march-larm { 0%, 100% { transform: rotate(-30deg); } 50% { transform: rotate(20deg); } }
        @keyframes march-rarm { 0%, 100% { transform: rotate(30deg); } 50% { transform: rotate(-20deg); } }

        /* SHAKE - hip wiggle */
        @keyframes shake-body { 0%, 100% { transform: translateX(-8px) rotate(-3deg); } 50% { transform: translateX(8px) rotate(3deg); } }
        @keyframes shake-larm { 0%, 100% { transform: rotate(60deg); } 50% { transform: rotate(40deg); } }
        @keyframes shake-rarm { 0%, 100% { transform: rotate(-40deg); } 50% { transform: rotate(-60deg); } }
        @keyframes shake-lleg { 0%, 100% { transform: rotate(10deg); } 50% { transform: rotate(-10deg); } }
        @keyframes shake-rleg { 0%, 100% { transform: rotate(-10deg); } 50% { transform: rotate(10deg); } }

        /* STRETCH - arms wide */
        @keyframes stretch-larm { 0%, 100% { transform: rotate(80deg); } 50% { transform: rotate(100deg); } }
        @keyframes stretch-rarm { 0%, 100% { transform: rotate(-80deg); } 50% { transform: rotate(-100deg); } }
        @keyframes stretch-body { 0%, 100% { transform: translateY(0px) scaleY(1); } 50% { transform: translateY(-8px) scaleY(1.03); } }

        /* STOMP - power stomp */
        @keyframes stomp-lleg { 0%, 30% { transform: rotate(-50deg); } 50%, 100% { transform: rotate(0deg); } }
        @keyframes stomp-rleg { 0%, 30% { transform: rotate(0deg); } 50%, 100% { transform: rotate(-50deg); } }
        @keyframes stomp-body { 0%, 45% { transform: translateY(0); } 50% { transform: translateY(6px); } 60%, 100% { transform: translateY(0); } }
        @keyframes stomp-larm { 0%, 100% { transform: rotate(50deg); } 50% { transform: rotate(80deg); } }
        @keyframes stomp-rarm { 0%, 100% { transform: rotate(-50deg); } 50% { transform: rotate(-80deg); } }

        /* SIDE-STEP - lateral groove */
        @keyframes side-body { 0%, 100% { transform: translateX(-10px) rotate(-5deg); } 50% { transform: translateX(10px) rotate(5deg); } }
        @keyframes side-lleg { 0%, 100% { transform: rotate(15deg); } 50% { transform: rotate(-15deg); } }
        @keyframes side-rleg { 0%, 100% { transform: rotate(-15deg); } 50% { transform: rotate(15deg); } }
        @keyframes side-larm { 0%, 100% { transform: rotate(70deg); } 50% { transform: rotate(40deg); } }
        @keyframes side-rarm { 0%, 100% { transform: rotate(-40deg); } 50% { transform: rotate(-70deg); } }

        /* COOL-DOWN - slow breath */
        @keyframes cool-body { 0%, 100% { transform: translateY(0) scaleY(1); } 50% { transform: translateY(-3px) scaleY(1.01); } }
        @keyframes cool-larm { 0%, 100% { transform: rotate(60deg); } 50% { transform: rotate(90deg); } }
        @keyframes cool-rarm { 0%, 100% { transform: rotate(-60deg); } 50% { transform: rotate(-90deg); } }

        /* CLAP - clapping hands */
        @keyframes clap-larm { 0%, 40%, 100% { transform: rotate(30deg); } 20%, 60% { transform: rotate(70deg); } }
        @keyframes clap-rarm { 0%, 40%, 100% { transform: rotate(-30deg); } 20%, 60% { transform: rotate(-70deg); } }
        @keyframes clap-body { 0%, 100% { transform: scale(1); } 20% { transform: scale(1.04); } }

        /* ═══ KNEE LIFT - high alternate knee pumps ═══ */
        @keyframes knee-body { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        @keyframes knee-lleg {
            0%, 100% { transform: rotate(0deg); }
            30%, 50% { transform: rotate(-70deg); }
            70% { transform: rotate(0deg); }
        }
        @keyframes knee-rleg {
            0%, 50%, 100% { transform: rotate(0deg); }
            70%, 85% { transform: rotate(-70deg); }
        }
        @keyframes knee-larm { 0%, 100% { transform: rotate(-35deg); } 50% { transform: rotate(35deg); } }
        @keyframes knee-rarm { 0%, 100% { transform: rotate(35deg); } 50% { transform: rotate(-35deg); } }

        /* ═══ PLANT FOOT - one planted, other slides back ═══ */
        @keyframes plant-body { 0%, 100% { transform: translateX(-6px) rotate(-2deg); } 50% { transform: translateX(6px) rotate(2deg); } }
        @keyframes plant-lleg { 0%, 100% { transform: rotate(2deg); } 50% { transform: rotate(6deg); } }
        @keyframes plant-rleg { 0%, 100% { transform: rotate(28deg); } 50% { transform: rotate(44deg); } }
        @keyframes plant-larm { 0%, 100% { transform: rotate(62deg); } 50% { transform: rotate(72deg); } }
        @keyframes plant-rarm { 0%, 100% { transform: rotate(-28deg); } 50% { transform: rotate(-46deg); } }

        /* ═══ WRIST SHAKE - arms half-raised, rapid wrist flicking ═══ */
        @keyframes wrist-body { 0%, 100% { transform: translateX(-3px); } 50% { transform: translateX(3px); } }
        @keyframes wrist-larm {
            0%, 33%, 66%, 100% { transform: rotate(62deg); }
            16%, 50%, 83% { transform: rotate(78deg); }
        }
        @keyframes wrist-rarm {
            0%, 33%, 66%, 100% { transform: rotate(-62deg); }
            16%, 50%, 83% { transform: rotate(-78deg); }
        }
        @keyframes wrist-lleg { 0%, 100% { transform: rotate(5deg); } 50% { transform: rotate(-5deg); } }
        @keyframes wrist-rleg { 0%, 100% { transform: rotate(-5deg); } 50% { transform: rotate(5deg); } }

        /* Classes assigned to SVG groups */
        #g-body.anim-idle   { animation: idle-body 1.2s ease-in-out infinite; }
        #g-larm.anim-idle   { animation: idle-larm 1.2s ease-in-out infinite; }
        #g-rarm.anim-idle   { animation: idle-rarm 1.2s ease-in-out infinite; }

        #g-body.anim-march  { animation: march-body 0.5s ease-in-out infinite; }
        #g-lleg.anim-march  { animation: march-lleg 0.5s ease-in-out infinite; }
        #g-rleg.anim-march  { animation: march-rleg 0.5s ease-in-out infinite; }
        #g-larm.anim-march  { animation: march-larm 0.5s ease-in-out infinite; }
        #g-rarm.anim-march  { animation: march-rarm 0.5s ease-in-out infinite; }

        #g-body.anim-shake  { animation: shake-body 0.35s ease-in-out infinite; }
        #g-larm.anim-shake  { animation: shake-larm 0.35s ease-in-out infinite; }
        #g-rarm.anim-shake  { animation: shake-rarm 0.35s ease-in-out infinite; }
        #g-lleg.anim-shake  { animation: shake-lleg 0.35s ease-in-out infinite; }
        #g-rleg.anim-shake  { animation: shake-rleg 0.35s ease-in-out infinite; }

        #g-body.anim-stretch { animation: stretch-body 1s ease-in-out infinite; }
        #g-larm.anim-stretch { animation: stretch-larm 1s ease-in-out infinite; }
        #g-rarm.anim-stretch { animation: stretch-rarm 1s ease-in-out infinite; }

        #g-body.anim-stomp  { animation: stomp-body 0.5s ease-in-out infinite; }
        #g-lleg.anim-stomp  { animation: stomp-lleg 0.5s ease-in-out infinite; }
        #g-rleg.anim-stomp  { animation: stomp-rleg 0.5s ease-in-out infinite; }
        #g-larm.anim-stomp  { animation: stomp-larm 0.5s ease-in-out infinite; }
        #g-rarm.anim-stomp  { animation: stomp-rarm 0.5s ease-in-out infinite; }

        #g-body.anim-side   { animation: side-body 0.6s ease-in-out infinite; }
        #g-lleg.anim-side   { animation: side-lleg 0.6s ease-in-out infinite; }
        #g-rleg.anim-side   { animation: side-rleg 0.6s ease-in-out infinite; }
        #g-larm.anim-side   { animation: side-larm 0.6s ease-in-out infinite; }
        #g-rarm.anim-side   { animation: side-rarm 0.6s ease-in-out infinite; }

        #g-body.anim-cool   { animation: cool-body 2s ease-in-out infinite; }
        #g-larm.anim-cool   { animation: cool-larm 2s ease-in-out infinite; }
        #g-rarm.anim-cool   { animation: cool-rarm 2s ease-in-out infinite; }

        #g-body.anim-clap   { animation: clap-body 0.4s ease-in-out infinite; }
        #g-larm.anim-clap   { animation: clap-larm 0.4s ease-in-out infinite; }
        #g-rarm.anim-clap   { animation: clap-rarm 0.4s ease-in-out infinite; }

        /* ─── KNEE LIFT BINDINGS ─── */
        #g-body.anim-knee   { animation: knee-body 0.5s ease-in-out infinite; }
        #g-lleg.anim-knee   { animation: knee-lleg 1s ease-in-out infinite; }
        #g-rleg.anim-knee   { animation: knee-rleg 1s ease-in-out infinite 0.5s; }
        #g-larm.anim-knee   { animation: knee-larm 1s ease-in-out infinite; }
        #g-rarm.anim-knee   { animation: knee-rarm 1s ease-in-out infinite; }

        /* ─── PLANT FOOT BINDINGS ─── */
        #g-body.anim-plant  { animation: plant-body 1.2s ease-in-out infinite; }
        #g-lleg.anim-plant  { animation: plant-lleg 1.2s ease-in-out infinite; }
        #g-rleg.anim-plant  { animation: plant-rleg 1.2s ease-in-out infinite; }
        #g-larm.anim-plant  { animation: plant-larm 1.2s ease-in-out infinite; }
        #g-rarm.anim-plant  { animation: plant-rarm 1.2s ease-in-out infinite; }

        /* ─── WRIST SHAKE BINDINGS ─── */
        #g-body.anim-wrist  { animation: wrist-body 0.4s ease-in-out infinite; }
        #g-lleg.anim-wrist  { animation: wrist-lleg 0.5s ease-in-out infinite; }
        #g-rleg.anim-wrist  { animation: wrist-rleg 0.5s ease-in-out infinite; }
        #g-larm.anim-wrist  { animation: wrist-larm 0.3s ease-in-out infinite; }
        #g-rarm.anim-wrist  { animation: wrist-rarm 0.3s ease-in-out infinite; }

        /* ─── MOVE NAME DISPLAY ─── */
        .move-label {
            position: absolute;
            top: 15px; left: 50%;
            transform: translateX(-50%);
            background: rgba(46,125,50,0.85);
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: 0.85rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 5px 18px;
            border-radius: 50px;
            backdrop-filter: blur(6px);
            transition: opacity 0.4s;
            white-space: nowrap;
        }

        /* ─── BEAT PULSE RING ─── */
        .beat-ring {
            position: absolute;
            width: 180px; height: 180px;
            border-radius: 50%;
            border: 3px solid rgba(46,125,50,0);
            left: 50%; top: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }
        @keyframes beatRing {
            0%   { border-color: rgba(46,125,50,0.6); transform: translate(-50%,-50%) scale(0.8); }
            100% { border-color: rgba(46,125,50,0); transform: translate(-50%,-50%) scale(1.8); }
        }
        .beat-ring.pulse { animation: beatRing 0.6s ease-out; }

        /* ─── AI COACH BUBBLE ─── */
        .ai-bubble {
            background: #fff;
            border: 3px solid #2e7d32;
            border-radius: 20px;
            padding: 24px 28px;
            font-family: 'Nunito', sans-serif;
            font-size: 1.45rem;
            font-weight: 700;
            line-height: 1.9;
            color: #1b3022;
            min-height: 160px;
            position: relative;
        }
        .ai-bubble::before {
            content: '💬';
            position: absolute;
            top: -14px; left: 24px;
            font-size: 1.3rem;
        }

        /* ─── MODULE CARDS ─── */
        .module-card {
            background: #fff;
            border-radius: 14px;
            border: 2px solid transparent;
            padding: 16px 18px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .module-card:hover { border-color: #2e7d32; transform: translateX(4px); }
        .module-card.playing {
            border-color: #2e7d32;
            background: linear-gradient(90deg, #f0faf0, #fff);
            box-shadow: 0 4px 16px rgba(46,125,50,0.2);
        }
        .module-card .play-icon {
            width: 38px; height: 38px;
            background: #2e7d32;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; flex-shrink: 0;
            transition: transform 0.2s;
        }
        .module-card:hover .play-icon { transform: scale(1.12); }
        .module-card.playing .play-icon { background: #c62828; }

        /* ─── STEP TRACKER ─── */
        .step-tracker {
            display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px;
        }
        .step-dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: #dee2e6;
            transition: background 0.3s, transform 0.2s;
        }
        .step-dot.done { background: #2e7d32; transform: scale(1.2); }
        .step-dot.active { background: #ff4081; transform: scale(1.4); box-shadow: 0 0 6px #ff4081; }

        /* ─── SAVE TOAST ─── */
        .save-toast {
            position: fixed; bottom: 30px; right: 30px;
            background: #1b3022; color: white;
            border-left: 5px solid #2e7d32;
            border-radius: 12px;
            padding: 14px 22px;
            font-weight: 700;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1);
            z-index: 9999;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }
        .save-toast.show { transform: translateY(0); opacity: 1; }

        /* AI card glow when talking */
        .ai-card-active { box-shadow: 0 0 30px rgba(255,64,129,0.3), 0 4px 20px rgba(0,0,0,0.08); }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar shadow">
    <div class="sidebar-header text-center">
        <h4 class="mb-0 text-white">PARC DIVAS</h4>
        <small class="text-white-50">AI Dance System</small>
    </div>
    <nav class="nav flex-column mt-4">
        <a class="nav-link" href="user_dashboard.php"><i class="fas fa-th-large me-2"></i> Dashboard</a>
        <a class="nav-link active" href="tutorials.php"><i class="fas fa-play-circle me-2"></i> Tutorials</a>
        <a class="nav-link" href="progress_tracker.php"><i class="fas fa-chart-line me-2"></i> Progress</a>
        <a class="nav-link text-white" href="avail_promo.php">
            <i class="fas fa-rocket me-2"></i>
            <span>Avail Advance Steps</span>
            <span class="badge bg-warning text-dark ms-1">PROMO</span>
        </a>
        <a class="nav-link logout-link mt-auto" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </nav>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row g-4">

            <!-- LEFT: AI COACH + AVATAR -->
            <div class="col-lg-8">
                <div id="aiCard" class="bg-white p-4 rounded-4 shadow-sm position-relative" style="transition: box-shadow 0.4s;">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-success mb-0">
                            <i class="fas fa-robot me-2"></i>Live AI Coach
                        </h5>
                        <div class="d-flex align-items-center gap-3">
                            <span id="currentMoveName" class="badge bg-success-subtle text-success fw-bold" style="font-size:0.8rem; padding:6px 14px;">READY</span>
                            <button id="stopBtn" class="btn btn-danger btn-sm rounded-pill px-3" onclick="forceStop()" style="display:none;">
                                <i class="fas fa-stop me-1"></i>STOP
                            </button>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="loopToggle">
                                <label class="form-check-label small fw-bold text-muted" for="loopToggle">LOOP</label>
                            </div>
                        </div>
                    </div>

                    <!-- AVATAR STAGE -->
                    <div class="avatar-stage">
                        <!-- DISCO LIGHTS BACKGROUND -->
                        <div class="disco-light disco-light-1"></div>
                        <div class="disco-light disco-light-2"></div>
                        <div class="disco-light disco-light-3"></div>
                        <div class="disco-light disco-light-4"></div>
                        <div class="disco-light disco-light-5"></div>
                        <div class="disco-light disco-light-6"></div>
                        <div class="disco-mirror"></div>
                        <div class="disco-strobe"></div>

                        <span id="moveLabel" class="move-label" style="opacity:0;">IDLE</span>
                        <div class="beat-ring" id="beatRing"></div>

                        <!-- BEAT COUNTER -->
                        <div id="beatCounter" style="position:absolute; bottom:16px; left:50%; transform:translateX(-50%);
                            background: rgba(46,125,50,0.92); color:#fff; font-family:'Nunito',sans-serif;
                            font-weight:800; font-size:1.1rem; padding:5px 22px; border-radius:30px;
                            letter-spacing:5px; opacity:0; transition: opacity 0.35s;
                            box-shadow: 0 2px 12px rgba(46,125,50,0.4); white-space:nowrap; z-index:10;">
                            <span id="beatText">1-2-3-4</span>
                        </div>

                        <!-- CURRENT STEP INSTRUCTION -->
                        <div id="stepInstruction" style="position:absolute; bottom:60px; left:50%; transform:translateX(-50%);
                            background: rgba(0,0,0,0.72); color:#ffd54f; font-family:'Nunito',sans-serif;
                            font-weight:700; font-size:0.82rem; padding:5px 18px; border-radius:20px;
                            opacity:0; transition: opacity 0.35s; white-space:nowrap; max-width:92%; z-index:10;
                            text-align:center; letter-spacing:0.5px;">
                            <span id="stepInstructionText"></span>
                        </div>

                        <!-- SVG DANCING AVATAR -->
                        <svg id="avatarSvg" viewBox="0 0 200 300" width="220" height="330" xmlns="http://www.w3.org/2000/svg">
                            <!-- Glow filter -->
                            <defs>
                                <filter id="glow">
                                    <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
                                    <feMerge><feMergeNode in="coloredBlur"/><feMergeNode in="SourceGraphic"/></feMerge>
                                </filter>
                                <filter id="shadow">
                                    <feDropShadow dx="0" dy="4" stdDeviation="4" flood-color="#000" flood-opacity="0.4"/>
                                </filter>
                            </defs>

                            <!-- SHADOW on floor -->
                            <ellipse cx="100" cy="290" rx="38" ry="8" fill="rgba(0,0,0,0.35)" id="floorShadow"/>

                            <!-- BODY GROUP (torso + all limbs attached) -->
                            <g id="g-body" style="transform-origin: 100px 160px;" class="anim-idle">

                                <!-- LEFT ARM -->
                                <g id="g-larm" style="transform-origin: 85px 130px;" class="anim-idle">
                                    <line x1="85" y1="130" x2="60" y2="170" stroke="#8bc34a" stroke-width="9" stroke-linecap="round"/>
                                    <!-- Forearm -->
                                    <line x1="60" y1="170" x2="48" y2="200" stroke="#8bc34a" stroke-width="8" stroke-linecap="round"/>
                                    <!-- Hand Palm -->
                                    <circle cx="46" cy="204" r="7" fill="#ffd54f"/>
                                    <!-- Left Hand Fingers -->
                                    <line x1="42" y1="198" x2="38" y2="192" stroke="#ffd54f" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="44" y1="197" x2="42" y2="188" stroke="#ffd54f" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="46" y1="197" x2="46" y2="187" stroke="#ffd54f" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="48" y1="197" x2="50" y2="188" stroke="#ffd54f" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="50" y1="198" x2="54" y2="192" stroke="#ffd54f" stroke-width="2" stroke-linecap="round"/>
                                </g>

                                <!-- RIGHT ARM -->
                                <g id="g-rarm" style="transform-origin: 115px 130px;" class="anim-idle">
                                    <line x1="115" y1="130" x2="140" y2="170" stroke="#8bc34a" stroke-width="9" stroke-linecap="round"/>
                                    <line x1="140" y1="170" x2="152" y2="200" stroke="#8bc34a" stroke-width="8" stroke-linecap="round"/>
                                    <!-- Hand Palm -->
                                    <circle cx="154" cy="204" r="7" fill="#ffd54f"/>
                                    <!-- Right Hand Fingers -->
                                    <line x1="158" y1="198" x2="162" y2="192" stroke="#ffd54f" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="156" y1="197" x2="158" y2="188" stroke="#ffd54f" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="154" y1="197" x2="154" y2="187" stroke="#ffd54f" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="152" y1="197" x2="150" y2="188" stroke="#ffd54f" stroke-width="2" stroke-linecap="round"/>
                                    <line x1="150" y1="198" x2="146" y2="192" stroke="#ffd54f" stroke-width="2" stroke-linecap="round"/>
                                </g>

                                <!-- TORSO -->
                                <rect x="80" y="115" width="40" height="65" rx="14" fill="#2e7d32" filter="url(#shadow)"/>
                                <!-- Shirt detail stripe -->
                                <rect x="80" y="132" width="40" height="4" rx="2" fill="#8bc34a" opacity="0.6"/>

                                <!-- LEFT LEG -->
                                <g id="g-lleg" style="transform-origin: 90px 180px;">
                                    <line x1="90" y1="180" x2="82" y2="230" stroke="#1b5e20" stroke-width="11" stroke-linecap="round"/>
                                    <!-- Shoe -->
                                    <ellipse cx="79" cy="237" rx="13" ry="8" fill="#ffd54f" transform="rotate(-10, 79, 237)"/>
                                </g>

                                <!-- RIGHT LEG -->
                                <g id="g-rleg" style="transform-origin: 110px 180px;">
                                    <line x1="110" y1="180" x2="118" y2="230" stroke="#1b5e20" stroke-width="11" stroke-linecap="round"/>
                                    <ellipse cx="121" cy="237" rx="13" ry="8" fill="#ffd54f" transform="rotate(10, 121, 237)"/>
                                </g>

                                <!-- NECK -->
                                <rect x="93" y="100" width="14" height="20" rx="7" fill="#ffd54f"/>

                                <!-- HEAD -->
                                <circle cx="100" cy="88" r="28" fill="#ffd54f" filter="url(#glow)"/>
                                <!-- Hair band -->
                                <path d="M72 82 Q100 60 128 82" stroke="#ff4081" stroke-width="5" fill="none" stroke-linecap="round"/>
                                <!-- Ponytail -->
                                <ellipse cx="100" cy="62" rx="6" ry="10" fill="#ff4081" transform="rotate(10,100,62)"/>
                                <!-- Eyes -->
                                <circle cx="91" cy="86" r="4" fill="#1b3022"/>
                                <circle cx="109" cy="86" r="4" fill="#1b3022"/>
                                <!-- Eye shine -->
                                <circle cx="93" cy="84" r="1.5" fill="white"/>
                                <circle cx="111" cy="84" r="1.5" fill="white"/>
                                <!-- Smile -->
                                <path d="M91 97 Q100 106 109 97" stroke="#c62828" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                                <!-- Cheeks -->
                                <circle cx="84" cy="95" r="5" fill="#ff8a80" opacity="0.4"/>
                                <circle cx="116" cy="95" r="5" fill="#ff8a80" opacity="0.4"/>

                            </g><!-- /g-body -->

                            <!-- MUSIC NOTES (decorative, animated) -->
                            <g id="musicNotes" opacity="0">
                                <text x="150" y="70" font-size="18" fill="#8bc34a" opacity="0.8">♪</text>
                                <text x="30" y="90" font-size="14" fill="#ff4081" opacity="0.6">♫</text>
                                <text x="165" y="110" font-size="12" fill="#ffd54f" opacity="0.7">♩</text>
                            </g>
                        </svg>
                    </div>

                    <!-- BACKGROUND MUSIC PLAYER BAR -->
                    <div style="background: linear-gradient(135deg, #1b3022, #0d1f14); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px; border: 1px solid rgba(46,125,50,0.3); margin-top: 14px; margin-bottom: 12px;">
                        <button id="musicPlayBtn" onclick="toggleBackgroundMusic()" title="Toggle Background Music" style="width: 36px; height: 36px; border-radius: 50%; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; transition: all 0.2s; background: #2e7d32; color: white; flex-shrink: 0;">
                            <i class="fas fa-music" id="musicIcon"></i>
                        </button>
                        <div style="flex:1; min-width:0;">
                            <div style="color: #8bc34a; font-weight: 800; font-size: 0.82rem; letter-spacing: 0.5px;" id="trackName">🎵 Zumba Groove</div>
                            <div style="color: rgba(255,255,255,0.4); font-size: 0.7rem;" id="trackBpm">Background music ready • Click to play</div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-volume-up" style="color:rgba(255,255,255,0.4); font-size:0.75rem;"></i>
                            <input type="range" id="musicVolume" min="0" max="1" step="0.05" value="0.4" style="width: 60px; accent-color: #8bc34a; cursor: pointer;">
                        </div>
                    </div>

                    <!-- STEP PROGRESS DOTS -->
                    <div class="d-flex align-items-center gap-2 mt-2 mb-2">
                        <small class="text-muted fw-bold" style="font-size:0.75rem;">STEPS</small>
                        <div class="step-tracker" id="stepTracker"></div>
                    </div>

                    <!-- AI TEXT BUBBLE -->
                    <div class="ai-bubble mt-2">
                        <div id="aiOutput">Ready for a full session? <strong>Select a module</strong> to begin! 💃✨</div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: MODULE LIST -->
            <div class="col-lg-4">
                <h5 class="fw-bold mb-3" style="color: #1b3022;">Zumba Modules</h5>
                <div id="moduleList" style="max-height: 660px; overflow-y: auto;">
                    <?php foreach($tutorials as $row):
                        $steps = 300;
                        if(stripos($row['step_name'], 'Merengue') !== false) $steps = 350;
                        if(stripos($row['step_name'], 'V-Step') !== false) $steps = 450;
                        if(stripos($row['step_name'], 'Reggaeton') !== false) $steps = 550;
                    ?>
                    <div class="module-card" id="card-<?= $row['id'] ?>"
                         onclick="runRoutine('<?= $row['id'] ?>', '<?= addslashes($row['step_name']) ?>', '<?= addslashes($row['description']) ?>', <?= $steps ?>)">
                        <div>
                            <div class="fw-bold" style="color:#1b3022; font-size:0.95rem;"><?= htmlspecialchars($row['step_name']) ?></div>
                            <small class="text-success fw-bold text-uppercase" style="font-size:0.72rem; letter-spacing:1px;">
                                <?= $row['step_type'] ?: 'Featured' ?>
                            </small>
                        </div>
                        <div class="play-icon">
                            <i class="fas fa-play" style="font-size:0.8rem; margin-left:2px;"></i>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Mini stats -->
                <div class="bg-white rounded-4 shadow-sm p-3 mt-3">
                    <small class="text-muted fw-bold d-block mb-2" style="font-size:0.75rem; letter-spacing:1px;">SESSION</small>
                    <div class="d-flex justify-content-between">
                        <div class="text-center">
                            <div class="fw-bold text-success" id="sessionSteps">0</div>
                            <small class="text-muted" style="font-size:0.7rem;">STEPS</small>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold text-warning" id="sessionKcal">0.0</div>
                            <small class="text-muted" style="font-size:0.7rem;">KCAL</small>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold text-primary" id="sessionTime">0:00</div>
                            <small class="text-muted" style="font-size:0.7rem;">TIME</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SAVE TOAST -->
<div class="save-toast" id="saveToast">
    <i class="fas fa-fire me-2 text-warning"></i>
    <span id="toastMsg">Session saved!</span>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

/* ═══════════════════════════════════════════
   AVATAR ANIMATION CONTROLLER
═══════════════════════════════════════════ */
const MOVE_MAP = {
    // MARCH / STEPPING animations
    'march':   { anim: 'march',   label: 'MARCHING' },
    'step':    { anim: 'march',   label: 'STEPPING' },
    'walk':    { anim: 'march',   label: 'WALKING' },
    'forward': { anim: 'march',   label: 'FORWARD' },
    'back':    { anim: 'march',   label: 'STEP BACK' },
    'backward': { anim: 'march',   label: 'STEP BACK' },
    'advance': { anim: 'march',   label: 'ADVANCING' },
    'stomp':   { anim: 'stomp',   label: 'STOMPING' },
    'repeat':  { anim: 'march',   label: 'REPEAT' },
    'movement': { anim: 'march',   label: 'MOVING' },
    'wide':    { anim: 'march',   label: 'WIDE MARCH' },
    
    // HIP SHAKE / GROOVE animations
    'shake':   { anim: 'shake',   label: 'HIP SHAKE' },
    'hip':     { anim: 'shake',   label: 'HIP GROOVE' },
    'wiggle':  { anim: 'shake',   label: 'WIGGLE' },
    'groove':  { anim: 'shake',   label: 'GROOVING' },
    'move':    { anim: 'shake',   label: 'MOVING' },
    'bounce':  { anim: 'shake',   label: 'BOUNCING' },
    'sway':    { anim: 'shake',   label: 'SWAYING' },
    'gyrate':  { anim: 'shake',   label: 'GYRATING' },
    'rotate':  { anim: 'shake',   label: 'ROTATING' },
    'engage':  { anim: 'shake',   label: 'ENGAGING' },
    'wrist':   { anim: 'shake',   label: 'WRIST ACTION' },
    'shoulder': { anim: 'shake',  label: 'SHOULDER SHAKE' },
    
    // STRETCH / ARM RAISE animations
    'stretch': { anim: 'stretch', label: 'STRETCH' },
    'arm':     { anim: 'stretch', label: 'ARM RAISE' },
    'hand':    { anim: 'stretch', label: 'HAND WAVE' },
    'raise':   { anim: 'stretch', label: 'RAISING' },
    'reach':   { anim: 'stretch', label: 'REACHING' },
    'touch':   { anim: 'stretch', label: 'TOUCHING' },
    'up':      { anim: 'stretch', label: 'HANDS UP' },
    'overhead': { anim: 'stretch', label: 'OVERHEAD' },
    'wave':    { anim: 'stretch', label: 'WAVING' },
    
    // STOMP / POWER animations
    'knee':    { anim: 'stomp',   label: 'KNEE LIFT' },
    'lift':    { anim: 'stomp',   label: 'LIFT!' },
    'pulse':   { anim: 'stomp',   label: 'PULSE' },
    'jump':    { anim: 'stomp',   label: 'JUMP' },
    'power':   { anim: 'stomp',   label: 'POWER' },
    'kick':    { anim: 'stomp',   label: 'KICKING' },
    'thrust':  { anim: 'stomp',   label: 'THRUSTING' },
    'pound':   { anim: 'stomp',   label: 'POUNDING' },
    'powerful': { anim: 'stomp',  label: 'POWERFUL' },
    
    // SIDE STEP / LATERAL animations
    'side':    { anim: 'side',    label: 'SIDE STEP' },
    'lateral': { anim: 'side',    label: 'LATERAL' },
    'switch':  { anim: 'side',    label: 'SWITCH SIDE' },
    'cross':   { anim: 'side',    label: 'CROSS STEP' },
    'v-step':  { anim: 'side',    label: 'V-STEP' },
    'shuffle': { anim: 'side',    label: 'SHUFFLING' },
    'slide':   { anim: 'side',    label: 'SLIDING' },
    
    // COOL DOWN animations
    'cool':    { anim: 'cool',    label: 'COOL DOWN' },
    'breath':  { anim: 'cool',    label: 'BREATHE' },
    'slow':    { anim: 'cool',    label: 'SLOW DOWN' },
    'relax':   { anim: 'cool',    label: 'RELAX' },
    'rest':    { anim: 'cool',    label: 'REST' },
    'plant':   { anim: 'cool',    label: 'PLANT FOOT' },
    'pause':   { anim: 'cool',    label: 'PAUSE' },
    'recover': { anim: 'cool',    label: 'RECOVERING' },
    
    // CLAP animations
    'clap':    { anim: 'clap',    label: 'CLAP!' },
    'clapping': { anim: 'clap',   label: 'CLAPPING' },
    'snap':    { anim: 'clap',    label: 'SNAPPING' },
    'hands':   { anim: 'clap',    label: 'HANDS' },

    // KNEE LIFT animations
    'knee':    { anim: 'knee',    label: 'KNEE LIFT' },

    // PLANT FOOT animations
    'plant':   { anim: 'plant',   label: 'PLANT FOOT' },
    'planted': { anim: 'plant',   label: 'PLANTED' },

    // WRIST SHAKE animations
    'wrist':   { anim: 'wrist',   label: 'WRIST ACTION' },
    'rotate':  { anim: 'wrist',   label: 'ROTATING' },
};

/* ═══════════════════════════════════════════
   CHOREOGRAPHY DATABASE
═══════════════════════════════════════════ */
const CHOREOGRAPHY = {
    'Merengue': [
        { step: 1, action: 'March in place', description: 'Steady rhythmic stepping — feel every single beat', anim: 'march', count: '1 · 2 · 3 · 4', pause: 1800 },
        { step: 2, action: 'Shake hips', description: 'Swing those hips side to side — let the groove take over!', anim: 'shake', count: '5 · 6 · 7 · 8', pause: 1800 },
        { step: 3, action: 'Move hands naturally', description: 'Let your arms flow freely — accent the beat with your hands', anim: 'stretch', count: '1 · 2 · 3 · 4', pause: 1500 },
        { step: 4, action: 'Repeat the sequence', description: 'Keep that momentum going — Diva, you\'ve got this!', anim: 'march', count: '5 · 6 · 7 · 8', pause: 1500 }
    ],
    'Salsa': [
        { step: 1, action: 'Step out to the side', description: 'Wide lateral movement — lead with your right foot', anim: 'side', count: '1 · 2', pause: 1800 },
        { step: 2, action: 'Step back to center', description: 'Return to neutral position — feet back together', anim: 'march', count: '3 · 4', pause: 1800 },
        { step: 3, action: 'Shake shoulders', description: 'Engage upper body — alternate shoulder rolls with attitude!', anim: 'shake', count: '5 · 6', pause: 1800 },
        { step: 4, action: 'Stretch arms out for balance', description: 'Open arms wide — control and style, you look amazing!', anim: 'stretch', count: '7 · 8', pause: 1800 },
        { step: 5, action: 'Repeat the salsa combo', description: 'Flow with the salsa tempo — keep it smooth and sensual!', anim: 'side', count: '1 · 2 · 3 · 4', pause: 1500 }
    ],
    'Reggaeton': [
        { step: 1, action: 'Wide march', description: 'Power legs wide apart — stomp with intention!', anim: 'march', count: '1 · 2', pause: 1600 },
        { step: 2, action: 'Knee lift', description: 'Pump those knees up high — alternating left and right, feel the power!', anim: 'knee', count: '3 · 4', pause: 2000 },
        { step: 3, action: 'Stomp', description: 'Powerful floor strike — feel the bass vibrate through you!', anim: 'stomp', count: '5 · 6', pause: 1600 },
        { step: 4, action: 'Engage and shake core', description: 'Feel the rhythm in your core — hip circles, own the floor!', anim: 'shake', count: '7 · 8', pause: 1800 },
        { step: 5, action: 'Keep hands up', description: 'Fists pumping up high — maintain that upper body energy!', anim: 'stretch', count: '1 · 2', pause: 1600 },
        { step: 6, action: 'Pulse with the rhythm', description: 'Lock into the reggaeton beat — stay low, stay fierce!', anim: 'stomp', count: '3 · 4 · 5 · 6 · 7 · 8', pause: 2000 }
    ],
    'Cumbia': [
        { step: 1, action: 'Keep one foot planted', description: 'Plant your right foot firmly — establish your base, stay grounded', anim: 'plant', count: '1 · 2', pause: 2000 },
        { step: 2, action: 'Step the other foot back', description: 'Smooth backward motion — left foot slides back with a glide', anim: 'march', count: '3 · 4', pause: 1800 },
        { step: 3, action: 'Stretch one arm forward', description: 'Reach forward with purpose — feel the cumbia flow through your arm', anim: 'stretch', count: '5 · 6', pause: 1800 },
        { step: 4, action: 'Shake wrist', description: 'Wrist rolls and hand flicks — add that beautiful cumbia fluidity!', anim: 'wrist', count: '7 · 8', pause: 1800 },
        { step: 5, action: 'Switch legs and repeat', description: 'Mirror the movement on both sides — left then right, keep it flowing!', anim: 'side', count: '1 · 2 · 3 · 4', pause: 1800 }
    ],
    'V-Step': [
        { step: 1, action: 'Step forward wide', description: 'Right foot out — left foot out — create your V shape, wide and proud!', anim: 'march', count: '1 · 2', pause: 1800 },
        { step: 2, action: 'Step back together', description: 'Right foot in — left foot in — return to neutral stance cleanly', anim: 'side', count: '3 · 4', pause: 1800 },
        { step: 3, action: 'Raise and stretch arms up', description: 'Both arms reach high overhead while stepping forward — feel the full stretch!', anim: 'stretch', count: '5 · 6', pause: 2000 },
        { step: 4, action: 'Clap hands at the end', description: 'Big clap on the return — celebrate every perfect V-Step!', anim: 'clap', count: '7 · 8', pause: 1500 }
    ],
    'Cool Down': [
        { step: 1, action: 'Slow marching', description: 'Gradual pace reduction — easy gentle steps, let the body slow down', anim: 'cool', count: '1 · 2 · 3 · 4', pause: 2500 },
        { step: 2, action: 'Deep breathing', description: 'Inhale deeply through your nose... hold... and exhale slowly through your mouth', anim: 'cool', count: '5 · 6 · 7 · 8', pause: 3500 },
        { step: 3, action: 'Stretch arms overhead', description: 'Both arms reach up as high as you can — open your chest wide, breathe in!', anim: 'stretch', count: '1 · 2 · 3 · 4', pause: 3000 },
        { step: 4, action: 'Rotate hands slowly', description: 'Gentle wrist circles — release all the tension from your hands and forearms', anim: 'wrist', count: '5 · 6 · 7 · 8', pause: 3000 },
        { step: 5, action: 'Gradually lower heart rate', description: 'Bring your energy all the way down gently — you did absolutely amazing today Diva!', anim: 'cool', count: '1 · 2 · 3 · 4 · 5 · 6 · 7 · 8', pause: 4000 }
    ]
};

const LIMBS = ['g-body', 'g-larm', 'g-rarm', 'g-lleg', 'g-rleg'];

function setAvatarPose(animName) {
    LIMBS.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        // Remove all anim classes
        for (let i = el.classList.length - 1; i >= 0; i--) {
            const cls = el.classList[i];
            if (cls.startsWith('anim-')) el.classList.remove(cls);
        }
        el.classList.add('anim-' + animName);
    });
}


function detectMove(text) {
    if (!text) return { anim: 'idle', label: 'DANCING' };
    
    const t = text.toLowerCase();
    
    // Check for bracket-style animation tags first: [MARCH], [SHAKE], etc.
    const bracketTag = text.match(/\[([A-Z][A-Za-z\-]*)\]/);
    if (bracketTag) {
        const tag = bracketTag[1].toLowerCase();
        // Look for this tag in MOVE_MAP
        if (MOVE_MAP[tag]) {
            return MOVE_MAP[tag];
        }
        // If not found, try to match to an animation type
        for (const [keyword, config] of Object.entries(MOVE_MAP)) {
            if (keyword === tag) return config;
        }
    }
    
    // Check for explicit animation tag [anim:ANIMATION]
    const animTag = text.match(/\[anim:([a-z\-]+)\]/i);
    if (animTag) {
        const anim = animTag[1].toLowerCase();
        // Try to find a label from MOVE_MAP, else use anim name as label
        let label = anim.toUpperCase();
        for (const [keyword, config] of Object.entries(MOVE_MAP)) {
            if (config.anim === anim) {
                label = config.label;
                break;
            }
        }
        return { anim, label };
    }
    
    // Fallback to keyword detection in text
    for (const [keyword, config] of Object.entries(MOVE_MAP)) {
        if (t.includes(keyword)) return config;
    }
    
    return { anim: 'idle', label: 'DANCING' };
}

function updateAvatar(text) {
    const move = detectMove(text);
    setAvatarPose(move.anim);

    // Update label
    const label = document.getElementById('moveLabel');
    label.textContent = move.label;
    label.style.opacity = '1';

    // Update badge
    document.getElementById('currentMoveName').textContent = move.label;

    // Beat ring pulse
    const ring = document.getElementById('beatRing');
    ring.classList.remove('pulse');
    void ring.offsetWidth;
    ring.classList.add('pulse');
    ring.addEventListener('animationend', () => ring.classList.remove('pulse'), { once: true });

    // Music notes show/hide
    document.getElementById('musicNotes').style.opacity = (move.anim !== 'idle') ? '1' : '0';
}

/* ═══════════════════════════════════════════
   WORKOUT ENGINE
═══════════════════════════════════════════ */
let isActive = false;
const synth = window.speechSynthesis;
let voices = [];
let loopTimer, typeTimer;
let activeCardId = null;
let choreoSteps = null; // Stores full choreography objects for direct anim control

// Session timer
let sessionSeconds = 0, timerInterval = null;
function startTimer() {
    sessionSeconds = 0;
    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        sessionSeconds++;
        const m = Math.floor(sessionSeconds / 60);
        const s = String(sessionSeconds % 60).padStart(2, '0');
        document.getElementById('sessionTime').textContent = m + ':' + s;
    }, 1000);
}

function loadVoices() { voices = synth.getVoices(); }
loadVoices();
if (speechSynthesis.onvoiceschanged !== undefined) speechSynthesis.onvoiceschanged = loadVoices;

function getBestVoice() {
    return voices.find(v => v.name.includes('Google US English')) ||
           voices.find(v => v.lang === 'en-US') ||
           voices[0];
}

const CHEERS = ["Whoo! ", "Let's go, Diva! ", "Keep that energy! ", "Amazing! ", "Work it! "];

function forceStop() {
    isActive = false;
    synth.cancel();
    clearTimeout(loopTimer);
    clearTimeout(typeTimer);
    clearInterval(timerInterval);
    setAvatarPose('idle');
    document.getElementById('moveLabel').style.opacity = '0';
    document.getElementById('currentMoveName').textContent = 'READY';
    document.getElementById('stopBtn').style.display = 'none';
    document.getElementById('aiCard').classList.remove('ai-card-active');
    document.getElementById('aiOutput').innerHTML = 'Ready for a full session? <strong>Select a module</strong> to begin! 💃✨';
    document.getElementById('musicNotes').style.opacity = '0';
    document.getElementById('beatCounter').style.opacity = '0';
    document.getElementById('stepInstruction').style.opacity = '0';
    choreoSteps = null;
    // Reset all module cards
    document.querySelectorAll('.module-card').forEach(c => c.classList.remove('playing'));
    document.querySelectorAll('.module-card .play-icon i').forEach(i => {
        i.className = 'fas fa-play';
        i.style.marginLeft = '2px';
    });
    activeCardId = null;
}

function buildStepDots(count) {
    const tracker = document.getElementById('stepTracker');
    tracker.innerHTML = '';
    for (let i = 0; i < count; i++) {
        const d = document.createElement('div');
        d.className = 'step-dot';
        d.id = 'dot-' + i;
        tracker.appendChild(d);
    }
}

function runRoutine(cardId, name, fullDesc, stepCount) {
    forceStop();
    isActive = true;

    // Highlight selected card
    activeCardId = cardId;
    const card = document.getElementById('card-' + cardId);
    if (card) {
        card.classList.add('playing');
        const icon = card.querySelector('.play-icon i');
        icon.className = 'fas fa-stop';
        icon.style.marginLeft = '0';
    }

    document.getElementById('stopBtn').style.display = 'inline-block';
    document.getElementById('aiCard').classList.add('ai-card-active');
    startTimer();

    // Update session counters
    document.getElementById('sessionSteps').textContent = stepCount;
    document.getElementById('sessionKcal').textContent = (stepCount * 0.04).toFixed(1);

    // Check if this routine has detailed choreography
    const routineName = Object.keys(CHOREOGRAPHY).find(key => 
        name.toLowerCase().includes(key.toLowerCase()) || 
        key.toLowerCase().includes(name.toLowerCase())
    );
    
    let sentences = [];
    if (routineName && CHOREOGRAPHY[routineName]) {
        // Use choreography steps — store full objects for direct anim control
        choreoSteps = CHOREOGRAPHY[routineName];
        sentences = choreoSteps.map(step =>
            `Step ${step.step}: ${step.action}. ${step.description}`
        );
    } else {
        choreoSteps = null;
        sentences = fullDesc.split('.').map(s => s.trim()).filter(s => s.length > 2);
    }
    
    buildStepDots(sentences.length);

    let idx = 0;

    function speakStep() {
        if (!isActive) return;

        // Mark previous done
        if (idx > 0) {
            const prev = document.getElementById('dot-' + (idx - 1));
            if (prev) { prev.classList.remove('active'); prev.classList.add('done'); }
        }

        if (idx >= sentences.length) {
            // All steps done - mark last dot done
            const last = document.getElementById('dot-' + (sentences.length - 1));
            if (last) { last.classList.remove('active'); last.classList.add('done'); }
            autoSave(name, stepCount);
            return;
        }

        // Mark current dot active
        const dot = document.getElementById('dot-' + idx);
        if (dot) dot.classList.add('active');

        const choreStep = choreoSteps ? choreoSteps[idx] : null;

        // ─── DIRECT AVATAR CONTROL from choreography ───
        if (choreStep && choreStep.anim) {
            setAvatarPose(choreStep.anim);
            // Derive a human-readable label from MOVE_MAP or fall back to anim name
            const animEntry = Object.values(MOVE_MAP).find(m => m.anim === choreStep.anim);
            const label = animEntry ? animEntry.label : choreStep.anim.toUpperCase();
            document.getElementById('moveLabel').textContent = label;
            document.getElementById('moveLabel').style.opacity = '1';
            document.getElementById('currentMoveName').textContent = label;
            document.getElementById('musicNotes').style.opacity = '1';
            // Beat counter
            document.getElementById('beatText').textContent = choreStep.count || '1 · 2 · 3 · 4';
            document.getElementById('beatCounter').style.opacity = '1';
            // Step instruction overlay
            document.getElementById('stepInstructionText').textContent =
                `Step ${choreStep.step}/${choreoSteps.length}: ${choreStep.action}`;
            document.getElementById('stepInstruction').style.opacity = '1';
            // Beat ring pulse
            const ring = document.getElementById('beatRing');
            ring.classList.remove('pulse');
            void ring.offsetWidth;
            ring.classList.add('pulse');
            ring.addEventListener('animationend', () => ring.classList.remove('pulse'), { once: true });
        } else {
            // Fallback: keyword-detect from text
            updateAvatar(sentences[idx]);
        }

        const raw = sentences[idx] + '.';
        const addCheer = Math.random() > 0.55;
        const cheer = addCheer ? CHEERS[Math.floor(Math.random() * CHEERS.length)] : '';
        const speech = cheer + raw;

        const pauseMs = choreStep ? (choreStep.pause || 1800) : 1800;

        // Typewriter display
        typeWriter(`[${name}] ${speech}`, 'aiOutput', () => {
            if (!isActive) return;
            const utter = new SpeechSynthesisUtterance(speech);
            utter.voice = getBestVoice();
            utter.pitch = 1.25;
            utter.rate  = 0.92;
            utter.volume = 1;

            utter.onend   = () => {
                if (!isActive) return;
                idx++;
                loopTimer = setTimeout(speakStep, pauseMs);
            };
            synth.speak(utter);
        });
    }

    speakStep();
}

function typeWriter(text, elId, cb) {
    let i = 0;
    const el = document.getElementById(elId);
    el.innerHTML = '';
    function go() {
        if (!isActive) return;
        if (i < text.length) {
            el.innerHTML += text.charAt(i++);
            typeTimer = setTimeout(go, 22);
        } else if (cb) cb();
    }
    go();
}

function autoSave(routine, steps) {
    const fd = new FormData();
    fd.append('routine', routine);
    fd.append('steps', steps);
    fd.append('time_consumed', sessionSeconds);

    fetch('finish_tutorials.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            showToast(data.status === 'success'
                ? `✅ Saved! Burned ${parseFloat(data.calories).toFixed(1)} kcal`
                : '⚠️ Save failed: ' + (data.message || ''));

            if (data.status === 'success') {
                // Check loop
                if (document.getElementById('loopToggle').checked && activeCardId) {
                    forceStop();
                } else {
                    setAvatarPose('cool');
                    document.getElementById('currentMoveName').textContent = 'DONE!';
                    document.getElementById('aiOutput').innerHTML = '<strong>Great job, Diva! 🎉</strong> Session complete! Redirecting to your progress...';
                    setTimeout(() => { window.location.href = 'progress_tracker.php'; }, 4000);
                }
            }
        })
        .catch(() => showToast('⚠️ Could not save session'));
}

function showToast(msg) {
    const t = document.getElementById('saveToast');
    document.getElementById('toastMsg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 4000);
}

// Start idle animation on load
setAvatarPose('idle');

/* ═══════════════════════════════════════════
   SOUND EFFECTS ENGINE
═══════════════════════════════════════════ */
let audioCtx = null;

function initAudioContext() {
    if (audioCtx) return;
    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
}

// Resume audio context on user interaction
document.addEventListener('click', function() {
    if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
}, { once: true });

// Play button click - cheerful beep
function playClickSound() {
    initAudioContext();
    const now = audioCtx.currentTime;
    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    
    osc.frequency.value = 800;
    osc.type = 'sine';
    gain.gain.setValueAtTime(0.15, now);
    gain.gain.exponentialRampToValueAtTime(0.01, now + 0.1);
    
    osc.connect(gain);
    gain.connect(audioCtx.destination);
    
    osc.start(now);
    osc.stop(now + 0.1);
}

// Success sound - uplifting chime
function playSuccessSound() {
    initAudioContext();
    const now = audioCtx.currentTime;
    
    // Create multiple frequencies for a richer sound
    const frequencies = [523.25, 659.25, 783.99]; // C5, E5, G5 chord
    
    frequencies.forEach((freq, idx) => {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        
        osc.frequency.value = freq;
        osc.type = 'sine';
        gain.gain.setValueAtTime(0.1, now);
        gain.gain.exponentialRampToValueAtTime(0.01, now + 0.4);
        
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        
        osc.start(now);
        osc.stop(now + 0.4);
    });
}

// Start routine - energetic "ready" tone
function playStartSound() {
    initAudioContext();
    const now = audioCtx.currentTime;
    
    // Two ascending notes
    const notes = [
        { freq: 440, time: 0 },      // A4
        { freq: 523.25, time: 0.15 } // C5
    ];
    
    notes.forEach(note => {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        
        osc.frequency.value = note.freq;
        osc.type = 'square';
        gain.gain.setValueAtTime(0.12, now + note.time);
        gain.gain.exponentialRampToValueAtTime(0.01, now + note.time + 0.15);
        
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        
        osc.start(now + note.time);
        osc.stop(now + note.time + 0.15);
    });
}

// Stop routine - descending warning tone
function playStopSound() {
    initAudioContext();
    const now = audioCtx.currentTime;
    
    // Two descending notes
    const notes = [
        { freq: 523.25, time: 0 },   // C5
        { freq: 440, time: 0.12 }    // A4
    ];
    
    notes.forEach(note => {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        
        osc.frequency.value = note.freq;
        osc.type = 'sine';
        gain.gain.setValueAtTime(0.12, now + note.time);
        gain.gain.exponentialRampToValueAtTime(0.01, now + note.time + 0.15);
        
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        
        osc.start(now + note.time);
        osc.stop(now + note.time + 0.15);
    });
}

// Level up celebration sound
function playCelebrationSound() {
    initAudioContext();
    const now = audioCtx.currentTime;
    
    // Ascending scale
    const notes = [
        { freq: 523.25, time: 0 },    // C5
        { freq: 587.33, time: 0.12 }, // D5
        { freq: 659.25, time: 0.24 }, // E5
        { freq: 783.99, time: 0.36 }  // G5
    ];
    
    notes.forEach(note => {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        
        osc.frequency.value = note.freq;
        osc.type = 'sine';
        gain.gain.setValueAtTime(0.15, now + note.time);
        gain.gain.exponentialRampToValueAtTime(0.01, now + note.time + 0.2);
        
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        
        osc.start(now + note.time);
        osc.stop(now + note.time + 0.2);
    });
}

// Error/warning sound
function playErrorSound() {
    initAudioContext();
    const now = audioCtx.currentTime;
    
    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    
    osc.frequency.setValueAtTime(300, now);
    osc.frequency.exponentialRampToValueAtTime(150, now + 0.2);
    osc.type = 'square';
    
    gain.gain.setValueAtTime(0.1, now);
    gain.gain.exponentialRampToValueAtTime(0.01, now + 0.2);
    
    osc.connect(gain);
    gain.connect(audioCtx.destination);
    
    osc.start(now);
    osc.stop(now + 0.2);
}

// Hook sound effects into existing functions
const originalForceStop = forceStop;
forceStop = function() {
    playStopSound();
    originalForceStop();
};

const originalRunRoutine = runRoutine;
runRoutine = function(...args) {
    playStartSound();
    // Auto-start background music when routine starts
    if (!backgroundMusicPlaying) {
        startBackgroundMusic();
    }
    originalRunRoutine(...args);
};

const originalAutoSave = autoSave;
autoSave = function(routine, steps) {
    playCelebrationSound();
    originalAutoSave(routine, steps);
};

const originalShowToast = showToast;
showToast = function(msg) {
    playClickSound();
    originalShowToast(msg);
};

// Add click sound to all buttons
document.addEventListener('click', (e) => {
    if (e.target.closest('button') || e.target.closest('.module-card')) {
        playClickSound();
    }
}, true);

/* ═══════════════════════════════════════════
   BACKGROUND MUSIC ENGINE
═══════════════════════════════════════════ */
let backgroundMusicPlaying = false;
let musicSequencer = null;
let musicStepIndex = 0;
let musicMasterGain = null;
const MUSIC_TEMPO = 128;
const MUSIC_BEAT_DURATION = 60 / MUSIC_TEMPO;

// Drum patterns (16 step loops)
const KICK_PATTERN = [1,0,0,0, 0,1,0,0, 1,0,0,0, 0,1,0,0];
const SNARE_PATTERN = [0,0,0,0, 1,0,0,0, 0,0,0,0, 1,0,0,0];
const HAT_PATTERN = [1,0,1,0, 1,0,1,0, 1,0,1,0, 1,0,1,0];
const BASS_NOTES = [80,80,90,80, 75,75,80,75, 80,85,80,75, 80,80,90,80];

function createMusicDrum(freq, time, duration) {
    if (!audioCtx) return;
    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    osc.frequency.setValueAtTime(freq, time);
    osc.frequency.exponentialRampToValueAtTime(40, time + duration);
    gain.gain.setValueAtTime(0.15, time);
    gain.gain.exponentialRampToValueAtTime(0.001, time + duration);
    osc.connect(gain);
    gain.connect(musicMasterGain);
    osc.start(time);
    osc.stop(time + duration);
}

function createMusicSnare(time) {
    if (!audioCtx) return;
    const bufLen = audioCtx.sampleRate * 0.15;
    const buf = audioCtx.createBuffer(1, bufLen, audioCtx.sampleRate);
    const data = buf.getChannelData(0);
    for (let i = 0; i < bufLen; i++) data[i] = Math.random() * 2 - 1;
    
    const src = audioCtx.createBufferSource();
    const gain = audioCtx.createGain();
    const filter = audioCtx.createBiquadFilter();
    filter.type = 'highpass';
    filter.frequency.value = 2000;
    src.buffer = buf;
    src.connect(filter);
    filter.connect(gain);
    gain.connect(musicMasterGain);
    gain.gain.setValueAtTime(0.08, time);
    gain.gain.exponentialRampToValueAtTime(0.001, time + 0.15);
    src.start(time);
    src.stop(time + 0.15);
}

function createMusicHat(time, vol = 0.06) {
    if (!audioCtx) return;
    const bufLen = audioCtx.sampleRate * 0.08;
    const buf = audioCtx.createBuffer(1, bufLen, audioCtx.sampleRate);
    const data = buf.getChannelData(0);
    for (let i = 0; i < bufLen; i++) data[i] = Math.random() * 2 - 1;
    
    const src = audioCtx.createBufferSource();
    const gain = audioCtx.createGain();
    const filter = audioCtx.createBiquadFilter();
    filter.type = 'highpass';
    filter.frequency.value = 8000;
    src.buffer = buf;
    src.connect(filter);
    filter.connect(gain);
    gain.connect(musicMasterGain);
    gain.gain.setValueAtTime(vol, time);
    gain.gain.exponentialRampToValueAtTime(0.001, time + 0.08);
    src.start(time);
    src.stop(time + 0.08);
}

function musicSequencerStep() {
    if (!backgroundMusicPlaying || !audioCtx) return;
    
    const now = audioCtx.currentTime;
    const step = musicStepIndex % 16;
    
    if (KICK_PATTERN[step]) createMusicDrum(150, now, 0.25);
    if (SNARE_PATTERN[step]) createMusicSnare(now);
    if (HAT_PATTERN[step]) createMusicHat(now, step % 4 === 0 ? 0.1 : 0.04);
    if (step % 4 === 0) createMusicDrum(BASS_NOTES[step], now, 0.3);
    
    musicStepIndex++;
    const stepDuration = (MUSIC_BEAT_DURATION / 4) * 1000;
    musicSequencer = setTimeout(musicSequencerStep, stepDuration);
}

function startBackgroundMusic() {
    initAudioContext();
    if (audioCtx.state === 'suspended') audioCtx.resume();
    
    if (!musicMasterGain) {
        musicMasterGain = audioCtx.createGain();
        musicMasterGain.connect(audioCtx.destination);
    }
    musicMasterGain.gain.value = parseFloat(document.getElementById('musicVolume').value);
    
    backgroundMusicPlaying = true;
    musicStepIndex = 0;
    musicSequencerStep();
    
    document.getElementById('musicIcon').className = 'fas fa-pause';
    document.getElementById('musicPlayBtn').style.background = '#c62828';
    document.getElementById('trackBpm').textContent = `${MUSIC_TEMPO} BPM • Background Groove`;
}

function stopBackgroundMusic() {
    backgroundMusicPlaying = false;
    clearTimeout(musicSequencer);
    document.getElementById('musicIcon').className = 'fas fa-music';
    document.getElementById('musicPlayBtn').style.background = '#2e7d32';
    document.getElementById('trackBpm').textContent = 'Background music ready • Click to play';
}

function toggleBackgroundMusic() {
    if (backgroundMusicPlaying) stopBackgroundMusic();
    else startBackgroundMusic();
}

// Volume control
document.getElementById('musicVolume').addEventListener('change', (e) => {
    const vol = parseFloat(e.target.value);
    if (musicMasterGain) musicMasterGain.gain.value = vol;
});

</script>
</body>
</html>