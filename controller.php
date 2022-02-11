<?php
class Controller { 
    public $database;
    public $articles = [];
    public $style_dir;
    public $id;
    public $result;


    function __construct($items, $db) {
        // store the articles
        $this->database = $db;
        $this->articles = $items;
    }

    function __destruct() {
        $this->database->closeConnection();
    }

    public function getAllTags() { 
        $tags = [];
        foreach ($this->articles as $key => $value) {
            if (isset($value['tag'])) {
                $split = explode(' ', $value['tag']);
                if (sizeof($split) == 1) {
                    if (!in_array($split[0], $tags)) {
                        $tags[] = $split[0];
                    }
                } else if (sizeof($split) > 1) {
                    foreach ($split as $tag) {
                        if (!in_array($tag, $tags)) {
                            $tags[] = $tag;
                        }
                    }
                }
                
            }
        }
        return $tags;
    }

    private function isValidURL($split) {
        $size = sizeof($split);
        if ($split[0] == 'articles' && $size == 1) return true;

        if ($size != 2) {
            return false;
        }
        if (!array_key_exists($split[1], $this->articles)) {
            return false;
        }
        if ($split[0] == 'article-edit' || $split[0] == 'article') {
            $this->id = $split[1];
            return true;
        }
        return false;
    }

    public function getCurrentUsersTags() {
        $tags = "";
        foreach ($this->articles as $key => $value) {
            if ($this->id == $key) {
                $tags .= $value['tag'] . " ";
            }
        }
        return trim($tags);
    }

    // returns the title of the current article
    public function getTitle() { 
        $title = $this->articles[$this->id]['name'];
        return $title;
    }

    // returns the content of the articles given
    public function getContent() {
        return $this->articles[$this->id]['content'];
    }

    private function getPath($url) { 
        $nav = "";
        if ($url == "articles") {
            $nav = "list";
            $this->style_dir = "./templates/";
        } else if ($url == "article") {
            $nav = "detail";
            $this->style_dir = "../templates/";
        } else { 
            $nav = "edit";
            $this->style_dir = "../templates/";
        }
        return "/templates/$nav/$url.php";
    }

    private function insertArticleName($title) {
        $sql = "INSERT INTO articles (name) VALUES (?)";
        $stmt = $this->database->connection->prepare($sql);
        $stmt->bind_param("s", $title);
        $stmt->execute();
    }

    private function getID($title) {
        foreach ($this->articles as $key => $value) {
            if ($value['name'] == $title) { 
                return $key;
            }
        }
        return -1;
    }

    private function createArticle($title) {
        $this->insertArticleName($title);   // add new article (title only)
        $this->articles = $this->database->loadArticles();  // load the articles from DB
        return $this->getID($title);    // get the ID of new article
    }

    private function updateDB($title, $content, $id, $tag) {
        $sql = "UPDATE articles SET name=?, content=?, tag=? WHERE id=?";
        $stmt = $this->database->connection->prepare($sql);
        $stmt->bind_param("sssi", $title, $content, $tag, $id);
        $stmt->execute();
        $stmt->close();
    }

    private function updateArticle($title, $content, $id, $tag) {
        $this->updateDB($title, $content, $id, $tag);  // update DB 
        $this->articles = $this->database->loadArticles();  // update local articles
    }

    private function deleteArticle($id) {
        $sql = "DELETE FROM articles WHERE id=?";
        $stmt = $this->database->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $this->articles = $this->database->loadArticles();
    }
    // -- OR tag LIKE ?";
    private function findChoice($choice) {
        $sql = "SELECT * From articles WHERE tag LIKE ?";

        $stmt = $this->database->connection->prepare($sql);
        $c = '%' . $choice . '%';
        $stmt->bind_param("s", $c);
        $stmt->execute();
        $statement_result = $stmt->get_result();
        $articles = [];
        
    
        while($row = $statement_result->fetch_assoc()) {
            $articles[$row['id']] = $row;
        }
        // print_r($articles);
        return $articles;
    }

    private function handleCreateRequest() {
        // this is from the dialog page
        $title = $_POST['title-name-create'];
        $articleID = $this->createArticle($title);
        if ($articleID == -1) { 
            echo "Article not found for title: $title";
        }
        header('Location: ./article-edit/' . $articleID, TRUE, 302);
    }

    private function handleSaveRequest() {
        // this is from edit page
        $title = $_POST['title-name-edit'];
        $content = $_POST['content'];
        $tags = $_POST['tags'];
        // $split_tags = $this->getTags($tags);

        $split = explode('/', $_GET['page']);
        $id = end($split);

        $this->updateArticle($title, $content, $id, $tags);

        header('Location: ../articles', TRUE, 302);  
    }

    private function display($path = "/templates/list/articles.php") {
        require (__DIR__ . '/templates/_header.php');
        require (__DIR__ . $path);
        require (__DIR__ . '/templates/_footer.php');
    }

    // Main function that checks the URL and handles the requests
    public function handleRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['create'])) {
                $this->handleCreateRequest();
            } 
            else if (isset($_POST['save'])) {
                $this->handleSaveRequest();
            } 
            else if (isset($_POST['edit-from-detail'])) {
                // this is from the detail page
                $split = explode('/', $$_POST['page']); // article/2 

                header('Location: ./article-edit/' . end($split), TRUE, 302);
            } 
            else if(isset($_POST['submit'])) {
                $choice = $_POST['tags-choice'];
                if ($choice == "all") {
                    header('Location: ./articles', TRUE, 302);
                }
                $result = $this->findChoice($choice);
                $this->articles = $result;
                $this->style_dir = "./templates/";
                $this->display(); 
            }
        } 
        else {
            $request_page = $_GET['page'];
            $split = explode('/', $request_page);

            if ($split[0] == "delete-article") {
                $this->deleteArticle($split[1]);
                echo(json_encode($this->articles));
                return true;
            }
    
            // check if the requested page valid relative path
            if (!isset($request_page) || !$this->isValidURL($split)) { 
                http_response_code(404);
                return;
            }
    
            $path = $this->getPath($split[0]);
    
            $this->display($path);
        }
    }
}
