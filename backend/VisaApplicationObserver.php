<?php
class VisaApplicationObserver
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function onCreated($applicationId, $dataEntryAgentName)
    {
        // Find user ID of the data entry agent
        $query = "SELECT id FROM users WHERE name = :name LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['name' => $dataEntryAgentName]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $userId = $user['id'];
            $message = "New visa application created: #$applicationId";
            // Store the notification
            $this->addNotification($userId, $message);
        } else {
            error_log("User not found for name: " . $dataEntryAgentName);
        }
    }
    // Watch for application status updates
    public function onStatusUpdated($applicationId, $userId, $newStatus)
    {
        $message = "Visa application #$applicationId status updated to: $newStatus";
        $this->addNotification($userId, $message);
    }

    // Insert notification into the database
    private function addNotification($user_id, $message)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$user_id, $message]);
            if ($stmt->rowCount() > 0) {
                echo "Notification added successfully!";
            } else {
                echo "Failed to insert notification!";
            }
        } catch (PDOException $e) {
            error_log("Notification Insert Error: " . $e->getMessage());
            echo "Error inserting notification!";
        }
    }
}
