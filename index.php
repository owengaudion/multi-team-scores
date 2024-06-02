<!DOCTYPE html>
    <html>
    <head>
        <title>Multi-team Scoreboard</title>
        <link rel="stylesheet" href="assets/styles/main.css">
        <script src="assets/js/main.js"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
        <meta name="viewport" content="width=device-width" />
    </head>
    <body>

<?php
session_start();

// Define the file to store team data
$teamFile = 'teams.txt';

// Password for access
$password = 'Password';

// Check for password submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $password) {
        $_SESSION['authenticated'] = true;
    } else {
        $error = 'Invalid password.';
    }
}

// If not authenticated, show the password form
if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    ?>
    
        <div class="password-form">
            <form method="post">
                <div class="error"><?php echo isset($error) ? $error : ''; ?></div>
                <input type="password" name="password" placeholder="Enter password">
                <input type="submit" value="Submit">
            </form>
        </div>

    <?php
    exit();
}

// Function to load teams from a file
function loadTeams() {
    global $teamFile;
    if (file_exists($teamFile)) {
        $teams = json_decode(file_get_contents($teamFile), true);
        return is_array($teams) ? $teams : ['team1' => 'Team 1'];
    } else {
        return ['team1' => 'Team 1'];
    }
}

// Function to save teams to a file
function saveTeams($teams) {
    global $teamFile;
    file_put_contents($teamFile, json_encode($teams));
}

// Function to get the current counter value for a team
function getCounter($team) {
    $filename = $team . '_counter.txt';
    if (file_exists($filename)) {
        $counter = file_get_contents($filename);
        return (int)$counter;
    } else {
        return 0;
    }
}

// Function to update the counter value for a team
function updateCounter($team, $value) {
    $filename = $team . '_counter.txt';
    file_put_contents($filename, $value);
}

// Function to reset all counters
function resetCounters() {
    $teams = loadTeams();
    foreach (array_keys($teams) as $team) {
        updateCounter($team, 0);
    }
}

// Load teams
$teams = loadTeams();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['password'])) {
    if (isset($_POST['team']) && isset($_POST['increment'])) {
        $team = $_POST['team'];
        $incrementValue = (int)$_POST['increment'];
        $currentCounter = getCounter($team);
        $newCounter = $currentCounter + $incrementValue;
        updateCounter($team, $newCounter);
    } elseif (isset($_POST['team']) && isset($_POST['clear'])) {
        $team = $_POST['team'];
        updateCounter($team, 0);
    } elseif (isset($_POST['add_team']) && count($teams) < 4) {
        $newTeamKey = 'team' . (count($teams) + 1);
        $teams[$newTeamKey] = 'Team ' . (count($teams) + 1);
        saveTeams($teams);
    } elseif (isset($_POST['remove_team']) && count($teams) > 1) {
        $teamKey = $_POST['team_key'];
        unset($teams[$teamKey]);
        saveTeams($teams);
    } elseif (isset($_POST['global_reset'])) {
        $teams = ['team1' => 'Team 1'];
        saveTeams($teams);
        resetCounters();
    } elseif (isset($_POST['rename_team']) && isset($_POST['team_name']) && isset($_POST['team_key'])) {
        $teamKey = $_POST['team_key'];
        $teamName = $_POST['team_name'];
        $teams[$teamKey] = $teamName;
        saveTeams($teams);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
    <div class="container">
        <?php foreach ($teams as $teamKey => $teamName): ?>
        <div class="box">
            <button class="remove-team" onclick="removeTeam('<?php echo $teamKey; ?>')">&times;</button>
            <input type="text" class="team-name" value="<?php echo htmlspecialchars($teamName); ?>" onchange="renameTeam('<?php echo $teamKey; ?>', this.value)">
            <span class="score" id="counterDisplay<?php echo $teamKey; ?>"><?php echo getCounter($teamKey); ?></span>
            <form method="post">
                <input type="hidden" name="team" value="<?php echo $teamKey; ?>">
                <div class="plus-score">
                    <button type="submit" name="increment" value="1">+1</button>
                    <button type="submit" name="increment" value="5">+5</button>
                    <button type="submit" name="increment" value="10">+10</button>
                </div>
                <div class="minus-score">
                    <button type="submit" name="increment" value="-1">-1</button>
                    <button type="submit" name="increment" value="-5">-5</button>
                    <button type="submit" name="increment" value="-10">-10</button>
                </div>
                <button type="submit" name="clear">Clear</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <form method="post" class="controls">
        <button type="submit" name="add_team" class="add-team" <?php echo count($teams) >= 4 ? 'disabled' : ''; ?>>Add Team</button>
        <button type="submit" name="global_reset" class="global-reset">Global Reset</button>
    </form>

</body>
</html>
