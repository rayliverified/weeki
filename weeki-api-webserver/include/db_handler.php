<?php
class DbHandler
{

    private $conn;

    function __construct()
    {
        require_once dirname(__FILE__) . '/db_connect.php';
        $db         = new DbConnect();
        $this->conn = $db->connect();
    }

    public function createUser($name, $username, $email, $password, $icon)
    {
        require_once 'PassHash.php';
        $response = array();
        if (!$this->isUserExists($email, $username)) {
            $password_hash = PassHash::hash($password);
            $api           = $this->generateApiKey();
            $stmt          = $this->conn->prepare("INSERT INTO users(name, username, email, password, icon, api) values(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $username, $email, $password_hash, $icon, $api);
            $result = $stmt->execute();
            $stmt->close();
            if ($result) {
                $response          = $this->getApi($username);
                $response["error"] = false;
                $response["user"]  = $this->getUserByEmail($email);
            } else {
                $response["error"]   = true;
                $response["message"] = UNKNOWN_ERROR;
            }
        } else {
            $response["error"] = true;
            $response["code"]  = USER_ALREADY_EXISTS;
        }
        return $response;
    }

    private function generateApiKey()
    {
        return md5(uniqid(rand(), true));
    }

    public function userLogin($username, $password)
    {
        require_once 'PassHash.php';
        $response = array();
        $stmt     = $this->conn->prepare("SELECT password, disabled FROM users WHERE username = '$username'");
        $stmt->execute();
        $stmt->bind_result($password_hash, $disabled);
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->fetch();
            $stmt->close();
            if ($disabled == 0) {
                if (PassHash::check_password($password_hash, $password)) {
                    $response            = $this->getApi($username);
                    $response['error']   = false;
                    $response['account'] = $this->getUserByUsername($username);
                } else {
                    $response["error"] = true;
                    $response["code"]  = PASSWORD_INCORRECT;
                }
            } else {
                $response["error"] = true;
                $response["code"]  = ACCOUNT_DISABLED;
            }
        } else {
            $response["error"] = true;
            $response["code"]  = USER_INVALID;
        }
        return $response;
    }

    public function updateGCMID($user_id, $gcm_id)
    {
        $response = array();
        $stmt     = $this->conn->prepare("UPDATE users SET registration_id = ? WHERE id = ?");
        $stmt->bind_param("si", $gcm_id, $user_id);
        if ($stmt->execute()) {
            $response["error"]   = false;
            $response["message"] = GCM_UPDATE_SUCCESSFUL;
        } else {
            $response["error"]   = true;
            $response["message"] = GCM_UPDATE_FAILED;
            $stmt->error;
        }
        $stmt->close();
        return $response;
    }

    public function checkApi($api)
    {
        $stmt = $this->conn->prepare("SELECT id from users WHERE api = ?");
        $stmt->bind_param("s", $api);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function getApi($user_id)
    {
        $stmt = $this->conn->prepare("SELECT api FROM users WHERE id = ? OR username = ?");
        $stmt->bind_param("ss", $user_id, $user_id);
        if ($stmt->execute()) {
            $api = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $api;
        } else {
            return NULL;
        }
    }

    public function getUserId($api)
    {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE api = ?");
        $stmt->bind_param("s", $api);
        if ($stmt->execute()) {
            $user_id = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $user_id;
        } else {
            return NULL;
        }
    }

    function isUserExists($email, $username)
    {
        $stmt = $this->conn->prepare("SELECT id from users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function getUser($user_id)
    {
        $stmt = $this->conn->prepare("SELECT id as user, username, name, email, registration_id, created_At, disabled, status, icon FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $stmt->bind_result($id, $username, $name, $email, $registration_id, $created_At, $disabled, $status, $icon);
            $stmt->fetch();
            $user                    = array();
            $user["user"]            = $id;
            $user["username"]        = $username;
            $user["name"]            = $name;
            $user["email"]           = $email;
            $user["registration_id"] = $registration_id;
            $user["created_At"]      = $created_At;
            $user["disabled"]        = $disabled;
            $user["status"]          = $status;
            $user["icon"]            = $icon;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    private function getUserByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT id as user, username, name, email, registration_id, created_At, disabled, status, icon FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->bind_result($id, $username, $name, $email, $registration_id, $created_At, $disabled, $status, $icon);
            $stmt->fetch();
            $user                    = array();
            $user["user"]            = $id;
            $user["username"]        = $username;
            $user["name"]            = $name;
            $user["email"]           = $email;
            $user["registration_id"] = $registration_id;
            $user["created_At"]      = $created_At;
            $user["disabled"]        = $disabled;
            $user["status"]          = $status;
            $user["icon"]            = $icon;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    function getUserByUsername($username)
    {
        $stmt = $this->conn->prepare("SELECT id as user, username, name, email, registration_id, created_At, disabled, status, icon FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            $stmt->bind_result($id, $username, $name, $email, $registration_id, $created_At, $disabled, $status, $icon);
            $stmt->fetch();
            $user                    = array();
            $user["user"]            = $id;
            $user["username"]        = $username;
            $user["name"]            = $name;
            $user["email"]           = $email;
            $user["registration_id"] = $registration_id;
            $user["created_At"]      = $created_At;
            $user["disabled"]        = $disabled;
            $user["status"]          = $status;
            $user["icon"]            = $icon;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }

    public function getUsersByUsername($usernames)
    {
        $users = array();
        if (sizeof($usernames) > 0) {
            $query = "SELECT id, username, name, email, registration_id, created_At, disabled, status, icon FROM users WHERE username IN (";
            foreach ($usernames as $username) {
                $query .= $username . ',';
            }
            $query = substr($query, 0, strlen($query) - 1);
            $query .= ')';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($user = $result->fetch_assoc()) {
                $tmp                    = array();
                $tmp["user"]            = $user['id'];
                $tmp["username"]        = $user['username'];
                $tmp["name"]            = $user['name'];
                $tmp["email"]           = $user['email'];
                $tmp["registration_id"] = $user['registration_id'];
                $tmp["created_At"]      = $user['created_At'];
                $tmp["disabled"]        = $user['disabled'];
                $tmp["status"]          = $user['status'];
                $tmp["icon"]            = $user['icon'];
                array_push($users, $tmp);
            }
        }
        return $users;
    }

    public function searchUsers($toFind)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username LIKE '%$toFind%' OR name LIKE '%$toFind%' OR email = ?");
        $stmt->bind_param("s", $toFind);
        $stmt->execute();
        $users = $stmt->get_result();
        $stmt->close();
        return $users;
    }

    public function updateUserIcon($user_id, $username, $image_Path)
    {
        $response = array();
        $stmt     = $this->conn->prepare("UPDATE users SET icon = ? WHERE id = ?");
        $stmt->bind_param("si", $image_Path, $user_id);
        if ($stmt->execute()) {
            $response["error"] = false;
            $response["code"]  = REQUEST_PASSED;
        } else {
            $response["error"] = true;
            $response["code"]  = REQUEST_FAILED;
            $stmt->error;
        }
        $stmt->close();
        return $response;
    }

    public function updateUserStatus($user_id, $username, $userstatus)
    {
        $response = array();
        $stmt     = $this->conn->prepare("UPDATE users SET status = ? WHERE username = ? AND id = ?");
        $stmt->bind_param("ssi", $userstatus, $username, $user_id);
        if ($stmt->execute()) {
            $response["error"] = false;
            $response["code"]  = REQUEST_PASSED;
        } else {
            $response["error"] = true;
            $response["code"]  = REQUEST_FAILED;
        }
        return $response;
    }

    public function updateUserPassword($user_id, $username, $userpassword)
    {
        require_once 'PassHash.php';
        $response = array();
        $password = PassHash::hash($userpassword);
        $stmt     = $this->conn->prepare("UPDATE users SET password = ? WHERE username = ? AND id = ?;");
        $stmt->bind_param("ssi", $password, $username, $user_id);
        if ($stmt->execute()) {
            $response["error"] = false;
            $response["code"]  = REQUEST_PASSED;
        } else {
            $response["error"] = true;
            $response["code"]  = REQUEST_FAILED;
        }
        return $response;
    }

    public function updateUserName($user_id, $username, $newname)
    {
        $response = array();
        $stmt     = $this->conn->prepare("UPDATE users SET name = ? WHERE username = ? AND id = ?;");
        $stmt->bind_param("ssi", $newname, $username, $user_id);
        if ($stmt->execute()) {
            $response["error"] = false;
            $response["code"]  = REQUEST_PASSED;
        } else {
            $response["error"] = true;
            $response["code"]  = REQUEST_FAILED;
        }
        return $response;
    }

    public function updateReceipt($group_id, $user_id, $msg_id)
    {
        $response;
        $query;
        if ($group_id == "-1") {
            $query = "UPDATE messages_receipt SET is_delivered = 1 WHERE message_id = ? AND user_id = ?";
        } else {
            $query = "UPDATE group_receipt SET is_delivered = 1 WHERE message_id = ? AND user_id = ?";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $msg_id, $user_id);
        if ($stmt->execute()) {
            $response["error"] = false;
            $response["code"]  = REQUEST_PASSED;
        } else {
            $response["error"] = true;
            $response["code"]  = REQUEST_FAILED;
        }
        $stmt->close();
        return $response;
    }

    public function getAllMessages($user_id)
    {
        $stmt = $this->conn->prepare("SELECT m.message_id, m.receiver_id, m.sender_id, u.name, m.msg_type, m.message, m.created_At, mr.is_delivered
FROM messages m LEFT JOIN users u ON u.id = m.sender_id LEFT JOIN messages_receipt mr ON mr.message_id = m.message_id
WHERE mr.is_delivered = 0 AND mr.user_id = ? ORDER BY m.message_id;");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $messages = $stmt->get_result();
        $stmt->close();
        return $messages;
    }

    public function getAllGroupConversation($user_id)
    {
        $stmt = $this->conn->prepare("SELECT gm.message_id, gm.group_id, g.name as group_name, g.icon as group_icon, g.description as group_description, g.creation as
group_creation, gm.user_id, u.username, gm.msg_type , gm.message, gm.created_at, gr.is_delivered
FROM group_messages gm LEFT JOIN groups g ON g.group_id = gm.group_id LEFT JOIN users u ON u.id = gm.user_id LEFT JOIN group_receipt gr ON gr.message_id = gm.message_id
WHERE gr.is_delivered = 0 AND gr.user_id = ? ORDER BY gm.group_id;");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $messages = $stmt->get_result();
        $stmt->close();
        return $messages;
    }

    public function addGroupMessage($group_id, $user_id, $message_type, $message)
    {
        $response = array();
        $crDate   = date("Y-m-d H:i:s");
        $stmt     = $this->conn->prepare("SELECT AddGroupMessage (?, ?, ?, ?, ?) as GM");
        $stmt->bind_param("iiiss", $group_id, $user_id, $message_type, $message, $crDate);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        if ($result != 0) {
            $response["error"]      = false;
            $response["message_id"] = $result;
            $response["message"]    = $message;
            $response["creation"]   = $crDate;
            $response["code"]       = MESSAGE_SENT;
        } else {
            $response["error"] = true;
            $response["code"]  = FAILED_MESSAGE_SEND;
        }
        return $response;
    }

    public function addPrivateMessage($from_user_id, $to_user_id, $message_type, $message)
    {
        $crDate = date("Y-m-d H:i:s");
        $stmt   = $this->conn->prepare("SELECT AddMessage (?, ?, ?, ?, ?) as PM");
        $stmt->bind_param("siiss", $to_user_id, $from_user_id, $message_type, $message, $crDate);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        if ($result != 0) {
            $response["error"]      = false;
            $response["message_id"] = $result;
            $response["creation"]   = $crDate;
            $response["code"]       = MESSAGE_SENT;
        } else {
            $response["error"] = true;
            $response["code"]  = FAILED_MESSAGE_SEND;
        }
        return $response;
    }

    public function getGroupMembers($group_id)
    {
        $members = array();
        $stmt    = $this->conn->prepare("SELECT gm.group_id, gm.user_id, u.username, u.registration_id FROM group_members gm LEFT JOIN
users u ON u.id = gm.user_id WHERE group_id = ? GROUP BY gm.group_id, gm.user_id;");
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($user = $result->fetch_assoc()) {
            $tmp                    = array();
            $tmp["group_id"]        = $user["group_id"];
            $tmp["user_id"]         = $user['user_id'];
            $tmp["username"]        = $user['username'];
            $tmp["registration_id"] = $user['registration_id'];
            array_push($members, $tmp);
        }
        $stmt->close();
        return $members;
    }

    public function getGroupInformation($group_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM groups WHERE group_id = ?");
        $stmt->bind_param("i", $group_id);
        if ($stmt->execute()) {
            $stmt->bind_result($id, $name, $icon, $description, $creation);
            $stmt->fetch();
            $group                = array();
            $group["group_id"]    = $id;
            $group["name"]        = $name;
            $group["icon"]        = $icon;
            $group["description"] = $description;
            $group["creation"]    = $creation;
            $stmt->close();
            return $group;
        } else {
            return NULL;
        }
    }

    public function createGroup($group_name, $group_icon, $group_description, $group_creator, $members)
    {
        $response = array();
        $stmt     = $this->conn->prepare("SELECT CreateGroup (?, ?, ?, ?) as CG");
        $stmt->bind_param("sssi", $group_name, $group_icon, $group_description, $group_creator);
        $stmt->execute();
        $stmt->bind_result($group_id);
        $stmt->fetch();
        $stmt->close();
        $query = "INSERT INTO group_members (group_id, user_id) VALUES ";
        foreach ($members as $user_id) {
            $query .= '(' . $group_id . ',' . $user_id['user'] . '),';
        }
        $query = substr($query, 0, strlen($query) - 2);
        $query .= ')';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result       = $stmt->get_result();
        $result['CG'] = $group_id;
        return $result;
    }

    public function updateGroupIcon($group_id, $user_id, $icon)
    {
        $response = array();
        $stmt     = $this->conn->prepare("UPDATE groups SET icon = ? WHERE group_id = (SELECT group_id FROM group_members WHERE user_id = ? AND group_id = ?);");
        $stmt->bind_param("sii", $icon, $user_id, $group_id);
        if ($stmt->execute()) {
            $response["error"] = false;
            $response["code"]  = REQUEST_PASSED;
        } else {
            $response["error"] = true;
            $response["code"]  = REQUEST_FAILED;
        }
        return $response;
    }

    public function updateGroupStatus($group_id, $user_id, $status)
    {
        $response = array();
        $stmt     = $this->conn->prepare("UPDATE groups SET description = ? WHERE group_id = (SELECT group_id FROM group_members WHERE user_id = ? AND group_id = ?);");
        $stmt->bind_param("sii", $status, $user_id, $group_id);
        if ($stmt->execute()) {
            $response["error"]  = false;
            $response["code"]   = REQUEST_PASSED;
            $response["status"] = $status;
        } else {
            $response["error"] = true;
            $response["code"]  = REQUEST_FAILED;
        }
        return $response;
    }

    public function updateGroupLeave($group_id, $user_id)
    {
        $response = array();
        $stmt     = $this->conn->prepare("DELETE FROM group_members WHERE user_id = ? AND group_id = ?");
        $stmt->bind_param("ii", $user_id, $group_id);
        if ($stmt->execute()) {
            $response["error"] = false;
            $response["code"]  = REQUEST_PASSED;
        } else {
            $response["error"] = true;
            $response["code"]  = REQUEST_FAILED;
        }
        return $response;
    }

    public function updateGroupKick($group_id, $whom)
    {
        $response = array();
        $stmt     = $this->conn->prepare("DELETE FROM group_members WHERE user_id = ? AND group_id = ?");
        $stmt->bind_param("ii", $whom, $group_id);
        if ($stmt->execute()) {
            $response["error"] = false;
            $response["code"]  = REQUEST_PASSED;
        } else {
            $response["error"] = true;
            $response["code"]  = REQUEST_FAILED;
        }
        return $response;
    }

    public function updateGroupName($group_id, $user_id, $name)
    {
        $response = array();
        $stmt     = $this->conn->prepare("UPDATE groups SET name = ? WHERE group_id = (SELECT group_id FROM group_members WHERE user_id = ? AND group_id = ?);");
        $stmt->bind_param("sii", $name, $user_id, $group_id);
        if ($stmt->execute()) {
            $response["error"] = false;
            $response["code"]  = REQUEST_PASSED;
            $response["name"]  = $name;
        } else {
            $response["error"] = true;
            $response["code"]  = REQUEST_FAILED;
        }
        return $response;
    }

    public function updateGroupParticipants($group_id, $members)
    {
        $response = array();
        $query    = "INSERT INTO group_members (group_id, user_id) VALUES ";
        foreach ($members as $user_id) {
            $query .= '(' . $group_id . ',' . $user_id['user'] . '),';
        }
        $query = substr($query, 0, strlen($query) - 2);
        $query .= ')';
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute()) {
            $response["error"] = false;
            $response["code"]  = REQUEST_PASSED;
        } else {
            $response["error"] = true;
            $response["code"]  = REQUEST_FAILED;
        }
        return $response;
    }
}
?>
