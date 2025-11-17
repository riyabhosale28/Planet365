<?php

require_once __DIR__ . '/../db.php';


function addPoints($conn, $userId, $points, $activityType, $meta = null) {

    $conn->begin_transaction();

    // 1. Update points in users table
    $stmt = $conn->prepare("UPDATE users SET points = points + ? WHERE userid = ?");
    if (!$stmt) {
        $conn->rollback();
        return false;
    }

    $stmt->bind_param("ii", $points, $userId);
    $ok1 = $stmt->execute();
    $stmt->close();

    // 2. Insert into activity log table
    $meta_json = $meta ? json_encode($meta) : null;

    $stmt2 = $conn->prepare(
        "INSERT INTO user_activity (user_id, activity_type, points, meta) 
         VALUES (?, ?, ?, ?)"
    );
    if (!$stmt2) {
        $conn->rollback();
        return false;
    }

    $stmt2->bind_param("isis", $userId, $activityType, $points, $meta_json);
    $ok2 = $stmt2->execute();
    $stmt2->close();

    // 3. Commit or rollback
    if ($ok1 && $ok2) {
        $conn->commit();
        return true;
    } else {
        $conn->rollback();
        return false;
    }
}


/*
    CHECK IF USER HAS BADGE
*/
function hasBadge($conn, $userId, $badgeCode) {
    $stmt = $conn->prepare("
        SELECT ub.id 
        FROM user_badges ub
        JOIN badge_definitions bd ON ub.badge_id = bd.id
        WHERE ub.user_id = ? AND bd.code = ?
    ");
    $stmt->bind_param("is", $userId, $badgeCode);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}


/*
    AWARD BADGE TO USER
*/
function awardBadge($conn, $userId, $badgeCode) {

    // initialize to avoid "unassigned variable" error
    $badgeId = null;

    // 1. Get badge ID
    $stmt = $conn->prepare("SELECT id FROM badge_definitions WHERE code = ?");
    $stmt->bind_param("s", $badgeCode);
    $stmt->execute();
    $stmt->bind_result($badgeId);

    // If fetch fails → badge doesn't exist
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
    $stmt->close();    

    // 2. Check if user already has this badge
    if (hasBadge($conn, $userId, $badgeCode)) {
        return false;
    }

    // 3. Award badge
    $stmt2 = $conn->prepare("
        INSERT INTO user_badges (user_id, badge_id)
        VALUES (?, ?)
    ");
    $stmt2->bind_param("ii", $userId, $badgeId);
    $ok = $stmt2->execute();
    $stmt2->close();
return $ok;
}


?>
