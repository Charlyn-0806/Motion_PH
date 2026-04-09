<?php
session_start();
// This will now work because login.php provides both
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

$user_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;

if (!$user_id) {
    // If the session isn't found, it redirects you
    header("Location: login.php?error=session_expired");
    exit();
}
$current_page = 'tutorials';

// Fetch routines from your dance_steps table
$stmt = $pdo->query("SELECT * FROM dance_steps");
$tutorials = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AI Zumba Coach - Master Routine</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

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

<div class="main-content">
    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-lg-8">
                <div id="aiCard" class="ai-card bg-white p-4 rounded-4 shadow-sm position-relative">
                    <div id="saveToast" style="display:none; position:absolute; top:20px; right:20px; z-index:100;" class="alert alert-success shadow-sm">
                        <i class="fas fa-fire me-2"></i> <span id="toastText">Session Saved!</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-success mb-0"><i class="fas fa-robot me-2"></i>Live AI Coach</h5>
                        <div class="d-flex align-items-center gap-3">
                            <button id="stopBtn" class="btn btn-danger btn-sm rounded-pill px-3" onclick="forceStop()" style="display:none;">STOP WORKOUT</button>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="loopToggle">
                                <label class="form-check-label small fw-bold text-muted">AUTO-REPEAT</label>
                            </div>
                        </div>
                    </div>

                    <div class="avatar-viewport shadow-sm rounded-3 overflow-hidden bg-dark" style="height: 400px;">
                        <img id="avatarDisplay" src="images/background.jpg" class="w-100 h-100 object-fit-cover lively-bounce">
                    </div>

                    <div class="ai-message-bubble mt-3 p-3 bg-light rounded-3 border-start border-success border-4">
                        <div id="aiOutput" class="fw-bold text-dark">Ready for a full session? Select a module to begin! 💃✨</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <h5 class="fw-bold mb-3" style="color: #1b3022 !important;">Zumba Modules</h5>
            <div class="module-list" style="max-height: 600px; overflow-y: auto;">
                    <?php foreach($tutorials as $row): 
                        // Set specific steps based on routine name
                        $steps = 300; // Default
                        if(stripos($row['step_name'], 'Merengue') !== false) $steps = 350;
                        if(stripos($row['step_name'], 'V-Step') !== false) $steps = 450;
                        if(stripos($row['step_name'], 'Reggaeton') !== false) $steps = 550;
                    ?>
                    <div class="card mb-3 shadow-sm border-0 module-card-item cursor-pointer" 
                         style="cursor: pointer;"
                         onclick="runMasterRoutine('<?= addslashes($row['step_name']) ?>', '<?= addslashes($row['description']) ?>', <?= $steps ?>)">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-0"><?= $row['step_name'] ?></h6>
                                <small class="text-success fw-bold text-uppercase"><?= $row['step_type'] ?></small>
                            </div>
                            <i class="fas fa-play-circle text-success fs-3"></i>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let isWorkoutActive = false; 
const synth = window.speechSynthesis;
let beatInterval, typewriterTimeout, loopTimeout;
let voices = [];
const cheers = ["Whoo!", "Let's go, Diva!", "Keep that heart rate up!", "Amazing energy!", "Work it!", "Looking sharp!"];

function loadVoices() { voices = synth.getVoices(); }
loadVoices();
if (speechSynthesis.onvoiceschanged !== undefined) { speechSynthesis.onvoiceschanged = loadVoices; }

function getBestVoice() {
    return voices.find(v => v.name.includes('Google US English') && v.name.includes('Female')) || 
           voices.find(v => v.name.toLowerCase().includes('female')) || 
           voices[0];
}

function forceStop() {
    isWorkoutActive = false;
    synth.cancel(); 
    if (beatInterval) clearInterval(beatInterval);
    clearTimeout(typewriterTimeout);
    clearTimeout(loopTimeout);
    document.getElementById('aiCard').classList.remove('ai-talking');
    document.getElementById('stopBtn').style.display = 'none';
    document.getElementById('aiOutput').innerHTML = "Ready for a full session? Select a module to begin!";
    document.getElementById('avatarDisplay').src = "images/background.jpg";
}

function runMasterRoutine(name, fullDesc, stepCount) {
    forceStop();
    isWorkoutActive = true;
    document.getElementById('stopBtn').style.display = 'inline-block';
    
    const steps = fullDesc.split('.').filter(s => s.trim().length > 0);
    let currentStep = 0;

    function executeStep() {
        if (!isWorkoutActive) return; 

        if (currentStep < steps.length) {
            let rawStep = steps[currentStep].trim() + ".";
            let speechText = (Math.random() > 0.6) ? cheers[Math.floor(Math.random() * cheers.length)] + " " + rawStep : rawStep;
            
            typeWriter(`[${name}] ${speechText}`, 'aiOutput', () => {
                const utter = new SpeechSynthesisUtterance(speechText);
                utter.voice = getBestVoice();
                utter.pitch = 1.3;
                utter.onstart = () => { updatePose(rawStep); document.getElementById('aiCard').classList.add('ai-talking'); };
                utter.onend = () => {
                    if (isWorkoutActive) {
                        currentStep++;
                        loopTimeout = setTimeout(executeStep, 3000); 
                    }
                };
                synth.speak(utter);
            });
        } else {
            autoSave(name, stepCount);
        }
    }
    executeStep();
}

function typeWriter(text, elementId, callback) {
    let i = 0;
    const el = document.getElementById(elementId);
    el.innerHTML = "";
    function type() {
        if (isWorkoutActive && i < text.length) {
            el.innerHTML += text.charAt(i++);
            typewriterTimeout = setTimeout(type, 30);
        } else if (callback) callback();
    }
    type();
}

function updatePose(text) {
    const img = document.getElementById('avatarDisplay');
    if (text.includes("March")) img.src = "images/march_pose.jpg";
    else if (text.includes("Shake")) img.src = "images/shake_move.jpg";
    else if (text.includes("Stretch")) img.src = "images/stretch_pose.jpg";
    else img.src = "images/dance_pose.jpg"; // Default dance pose
}

// --- Integration into your Stop Workout Logic ---

function stopWorkout() {
    // 1. Stop your timer logic
    if (typeof timerInterval !== 'undefined') clearInterval(timerInterval);

    // 2. Clean the data (Removes letters/spaces so only numbers remain)
    const rawSteps = document.getElementById('stepCount')?.innerText || "0";
    const rawTime = document.getElementById('minutesCount')?.innerText || "0";
    
    const cleanSteps = parseInt(rawSteps.replace(/\D/g, '')) || 0;
    const cleanTime = parseInt(rawTime.replace(/\D/g, '')) || 0;
    const routineName = document.getElementById('currentRoutine')?.innerText || "Zumba Session";

    console.log("Saving Cleaned Data:", { cleanSteps, cleanTime });

    // 3. Trigger Save
    autoSave(routineName, cleanSteps, cleanTime);
}

// --- The autoSave function you provided, updated to handle time ---

function autoSave(routine, steps, time) {
    console.log("Attempting to save:", routine, steps, time); 

    const formData = new FormData();
    formData.append('routine', routine);
    formData.append('steps', steps);
    formData.append('time_consumed', time); // Added time to the payload

    fetch('finish_tutorial.php', { 
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log("Server Response:", data); 
        if(data.status === 'success') {
            const toast = document.getElementById('saveToast');
            if(toast) {
                document.getElementById('toastText').innerText = `Session Saved! Burned ${data.calories} kcal!`;
                toast.style.display = 'block';
            }
            
            // Redirect after 3 seconds so the user can see the progress tracker update
            setTimeout(() => { 
                window.location.href = 'progress_tracker.php'; 
            }, 3000);
        } else {
            console.error("Save failed:", data.message);
            alert("Error saving: " + data.message);
        }
    })
    .catch(err => console.error("Fetch Error:", err));
}
    
</script>
</body>
</html>