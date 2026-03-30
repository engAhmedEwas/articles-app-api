<?php

require_once 'Controller.php';
require_once __DIR__ . '/../models/Article.php';

class ArticleController extends Controller {
    private $articleModel;

    public function __construct($db) {
        $this->articleModel = new Article($db);
    }

    public function index() {

        $data = $this->articleModel->all();

        if ($data) {
            $this->jsonResponse(200, "Opration Compaleted", $data);
        } else {
            $this->jsonResponse(404, "No articles found");
        }
    }

    public function show($id) {
        $data = $this->articleModel->findOrFail($id);

        if ($data) {
            $this->jsonResponse(200, "Success", $data);
        } else {
            $this->jsonResponse(404, "Article not found");
        }
    }

    public function store() {
        $input = $this->getRequestInput();

        if (!$this->articleModel->canUserPost($input['author_id'] ?? 0)) {
            $this->jsonResponse(403, "User not authorized to article");
        }

        $request = new ArticleStoreRequest($this->db, $input);
        $validation = $request->validate();

        if ($validation->fails()) {
            return $this->jsonResponse(422, "Validation Error", $validation->errors());
        }

        if ($this->articleModel->create($input)) {
            $this->jsonResponse(201, "Article Created Successfully", $input);
        } else {
            $this->jsonResponse(500, "Failed to create article");
        }
    }

    public function update($id) {
        $article = $this->articleModel->findOrFail($id);
        if (!$article) {
            $this->jsonResponse(404, "Article not found");
        }

        $input = $this->getRequestInput();

        if ($this->articleModel->update($id, $input)) {
            $this->jsonResponse(200, "Article Updated Successfully",
             ["id" => $id, "changes" => $input]);
        } else {
            $this->jsonResponse(500, "Failed to update article");
        }
    }

    public function destroy($id) {
        $article = $this->articleModel->findOrFail($id);
        if (!$article) {
            $this->jsonResponse(404, "Article not found to delete");
        }

        if ($this->articleModel->delete($id)) {
            $this->jsonResponse(200, "Deleted successfully");
        } else {
            $this->jsonResponse(500, "Error during deletion");
        }
    }
}