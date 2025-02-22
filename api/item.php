<?php
include_once("./includes/connect-db.php");

class Item
{
    public function fetch($id = null)
    {
        global $pdo;
        $sql = "SELECT * FROM item";

        if ($id) {
            $sql .= " WHERE iditem = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetchAll();

            if ($result) {
                $result[0]["image"] = base64_encode($result[0]["image"]);
            }

            return $result;
        } else {
            $stmt = $pdo->query($sql);
            $result = $stmt->fetchAll();

            if ($result) {
                for ($idx = 0; $idx < count($result); $idx++) {
                    $result[$idx]["image"] = base64_encode($result[$idx]["image"]);
                }
            }

            return $result;
        }
    }

    public function insert($code, $name, $value, $image)
    {
        global $pdo;

        $sql = "INSERT INTO item (code, name, value, image) VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$code, $name, $value, $image]);

        return $pdo->lastInsertId();
    }

    public function update($id, $code, $name, $value, $image)
    {
        global $pdo;

        $sql = "UPDATE item SET code = ?, name = ?, value = ?, image = ? WHERE iditem = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$code, $name, $value, $image, $id]);

        return $stmt->rowCount() > 0;
    }

    public function delete($id)
    {
        global $pdo;

        $sql = "DELETE FROM item WHERE iditem = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}

function send_json_response($data, $status_code = 200)
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: X-Requested-With');
    header('Content-Type: application/json');
    http_response_code($status_code);
    echo json_encode($data);
    exit();
}


$METHOD = $_SERVER["REQUEST_METHOD"];
$item = new Item();

$id = isset($_GET['id']) ? $_GET['id'] : null;

switch ($METHOD) {
    case "GET":
        if ($id) {
            send_json_response($item->fetch($id));
        } else {
            send_json_response($item->fetch());
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['code'], $data['name'], $data['value'], $data['image'])) {
            $id = $item->insert($data['code'], $data['name'], $data['value'], $data['image']);
            send_json_response(["message" => "Item created", "id" => $id], 201);
        } else {
            send_json_response(["message" => "Bad Request: Missing parameters"], 400);
        }
        break;

    case "PUT":
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['id'], $data['code'], $data['name'], $data['value'], $data['image'])) {
            $updated = $item->update($data['id'], $data['code'], $data['name'], $data['value'], $data['image']);
            if ($updated) {
                send_json_response(["message" => "Item updated successfully"]);
            } else {
                send_json_response(["message" => "Item not found or no changes made"], 404);
            }
        } else {
            send_json_response(["message" => "Bad Request: Missing parameters"], 400);
        }
        break;

    case "DELETE":
        if ($id) {
            $deleted = $item->delete($id);
            if ($deleted) {
                send_json_response(["message" => "Item deleted successfully"]);
            } else {
                send_json_response(["message" => "Item not found"], 404);
            }
        } else {
            send_json_response(["message" => "Bad Request: Missing ID parameter"], 400);
        }
        break;

    default:
        send_json_response(["message" => "Bad Request: Invalid request method"], 405);
        break;
}
