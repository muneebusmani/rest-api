<?php
class ProductController
{
    /**
     * This method will return a single product when the user gives an id,
     * but when there is no id, then there must be a collection of products
     * to be returned to the user.
     * @param string $method The Request method given by the user.
     * @param string $id The id given by the user(optional).
     * @return void
     */
    public function __construct(private ProductGateway $gateway) {}

    public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method, $id);
        }
    }

    /**
     * This method is used to handle single resource requests
     *
     * This method is triggered when a user  specify an id for the product, then this method will return all products
     *
     * @param string $method The request Method by which is made
     * @param ?string $id The id of the product to fetch
     * @return void
     */
    private function processResourceRequest(string $method, ?string $id): void
    {
        $product = $this->gateway->get($id);
        if (!$product) {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found']);
            return;
        }
        switch ($method) {
            case 'GET':
                http_response_code(200);
                echo json_encode($product);
                break;
            case 'PATCH':
                $data = (array) json_decode(file_get_contents('php://input'), true);
                $errors = $this->getValidationErrors($data, false);
                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(['errors' => $errors]);
                    exit;
                }
                $rows = $this->gateway->update($product, $data);
                http_response_code(200);
                echo json_encode([
                    'message' => "Product $id updated successfully",
                    'id' => "$rows rows effected",
                ]);
                break;
            case 'DELETE':
                $rows = $this->gateway->delete($id);
                http_response_code(200);
                echo json_encode([
                    'message' => "Product $id deleted successfully",
                    'id' => "$rows rows effected",
                ]);
                break;
            default:
                http_response_code(405);
                header('Allow: GET, PATCH, DELETE');
                break;
        }
    }

    /**
     * This method is used to handle collection requests
     *
     * This method is triggered when a user doesnt specify an id for the product, then this method will return all products
     *
     * @param string $method The request Method by which is made
     * @return void
     */
    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case 'GET':
                echo json_encode($this->gateway->getAll());
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'), true);
                $errors = $this->getValidationErrors($data);
                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(['errors' => $errors]);
                    exit;
                }
                $id = $this->gateway->create($data);
                http_response_code(201);
                echo json_encode([
                    'message' => 'Product created successfully',
                    'id' => $id,
                ]);
                break;
            default:
                http_response_code(405);
                header('Allow: GET, POST');
                break;
        }
    }

    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];
        if ($is_new && empty($data['name'])) {
            $errors[] = 'Name is required';
        }
        if (array_key_exists('size', $data)) {
            if (filter_var($data['size'], FILTER_VALIDATE_INT) === false) {
                $errors[] = 'Size must be an integer';
            }
        }
        return $errors;
    }
}
