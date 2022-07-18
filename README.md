# (php설치 폴더) php.ini 파일 수정
    - 주석 해제
    extension=pdo_mysql

# (Apache설치 폴더) httpd.conf 파일 수정

    - 주석 해제       
    LoadModule rewrite_module modules/mod_rewrite.so

    - AllowOverride None > AllowOverride All 로 변경
    <Directory "${SRVROOT}/htdocs">
        AllowOverride All
    
# php.ini, httpd.conf 파일 수정 후 아파치 서버 리스타트

# root폴더에 .htaccess 파일 생성

    Options -MultiViews
    RewriteEngine On
    Options -Indexes
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteRule ^(.+)$ index.php?url=$1 [QSA,L]

# 권한 예시 (Controller.php 파일의 $needLoginUrlArr 스태틱 배열에 추가)

    private static $needLoginUrlArr = [
        "board",         
        "user/signup"
    ];
    
1차 주소값 board면 로그인 필요.
user/signup 주소면 로그인 필요


# Controller 예시

    <?php
    namespace application\controllers;
    require_once "application/utils/UrlUtils.php"; //GET RESTful 통신에서 PathVariable 값 가져올 때 사용

    class BoardController extends Controller {   
        public function rest() {

            $method = $_SERVER['REQUEST_METHOD'];
            switch($method) {
                case "POST":
                    return ["body" => "post"];                
                case "GET":

                    if($pathVal = isGetOne()) { //one
                        return ["body" => $pathVal];    
                    }

                    //list
                    return ["body" => "get list"];
                case "PUT":
                    return ["body" => "put"];
                case "DELETE":
                    return ["body" => "delete"];
            }        
        }

        public function list() {        
            $this->addAttribute(_TITLE, "리스트");
            $this->addAttribute("list", $this->model->selBoardList());
            $this->addAttribute(_JS, ["board/list"]);
            $this->addAttribute(_HEADER, $this->getView("template/header.php"));
            $this->addAttribute(_MAIN, $this->getView("board/list.php"));
            $this->addAttribute(_FOOTER, $this->getView("template/footer.php"));
            return "template/t1.php";
        }

        public function detail() {
            $i_board = $_GET["i_board"];

            $param = ["i_board" => $i_board];
            $this->addAttribute("data", $this->model->selBoard($param));
            $this->addAttribute(_JS, ["board/detail"]);
            return "board/detail.php";       
        }

        public function del() {
            $i_board = $_GET["i_board"];            
            $param = ["i_board" => $i_board];
            $this->model->delBoard($param);        
            return "redirect:/board/list";
        }

        public function mod() {
            $i_board = $_GET["i_board"];              
            $param = ["i_board" => $i_board];
            $this->addAttribute("data", $this->model->selBoard($param));
            $this->addAttribute(_TITLE, "수정");
            $this->addAttribute(_HEADER, $this->getView("template/header.php"));
            $this->addAttribute(_MAIN, $this->getView("board/mod.php"));
            $this->addAttribute(_FOOTER, $this->getView("template/footer.php"));
            return "template/t1.php";
        }

        public function modProc() {
            $i_board = $_POST["i_board"];
            $title = $_POST["title"];
            $ctnt = $_POST["ctnt"];
            $param = [
                "i_board" => $i_board,
                "title" => $title,
                "ctnt" => $ctnt,
            ];        
            $this->model->updBoard($param);
            return "redirect:/board/detail?i_board={$i_board}";
        }
    }
    ?>
    --------------------------------------------------------------------------------------------------------------------
    <?php
    namespace application\controllers;

    class UserController extends Controller {  
        public function signup() {
            $method = getMethod();
            switch($method) {
                case _GET:
                    return ;
                case _POST:
                    return ;
            }
        }
        public function join() {
            $this->addAttribute(_TITLE, "회원가입");
            $this->addAttribute(_HEADER, $this->getView("template/header.php"));
            $this->addAttribute(_MAIN, $this->getView("user/join.php"));
            $this->addAttribute(_FOOTER, $this->getView("template/footer.php"));
            return "template/t1.php";
        }

        public function joinProc() {
            $param = [
                "uid" => $_POST["uid"],
                "upw" => $_POST["upw"],
                "nm" => $_POST["nm"],
                "gender" => $_POST["gender"],
            ];

            //비밀번호 암호화
            $param["upw"] = password_hash($param["upw"], PASSWORD_BCRYPT);        
            $this->model->insUser($param);
            return "redirect:join";
        }

        public function login() {
            $this->addAttribute(_TITLE, "로그인");
            $this->addAttribute(_HEADER, $this->getView("template/header.php"));
            $this->addAttribute(_MAIN, $this->getView("user/login.php"));
            $this->addAttribute(_FOOTER, $this->getView("template/footer.php"));
            return "template/t1.php";
        }

        public function loginProc() {
            $param = [
                "uid" => $_POST["uid"],
                "upw" => $_POST["upw"]
            ];

            $dbUser = $this->model->selUser($param);

            if($dbUser === false) { //아이디 없음
                print "아이디 없음 <br>";
                return $this->login();

            } else if(!password_verify($param["upw"], $dbUser->upw)) { //비밀번호 다름
                print "비밀번호 다름 <br>";
                return $this->login();            
            }
            $this->flash(_LOGINUSER, $dbUser); //세션 등록
            return "redirect:/board/list";
        }

        public function logout() {
            $this->flash(_LOGINUSER);
            return "redirect:/board/list";
        }    
    }






# Model 예시
    <?php
    namespace application\models;
    use PDO;

    class BoardModel extends Model {   
        public function selBoardList() {
            $sql = "SELECT i_board, title, created_at 
                      FROM t_board
                     ORDER BY i_board DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        public function selBoard(&$param) {
            $sql = "SELECT A.i_board, A.title, A.ctnt, A.created_at
                         , B.nm
                      FROM t_board A
                     INNER JOIN t_user B
                        ON A.i_user = B.i_user
                     WHERE A.i_board = :a";        
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":a", $param["i_board"]);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        }

        public function updBoard(&$param) {        
            $sql = "UPDATE t_board 
                       SET title = :title
                         , ctnt = :ctnt 
                     WHERE i_board = :i_board";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":title", $param["title"]);
            $stmt->bindValue(":ctnt", $param["ctnt"]);
            $stmt->bindValue(":i_board", $param["i_board"]);
            $stmt->execute();
        }

        public function delBoard(&$param) {
            $sql = "DELETE FROM t_board WHERE i_board = :i_board";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":i_board", $param["i_board"]);
            $stmt->execute();
        }
    }

# View 예시
    # header.php (세션 사용)    
    <div>
        <?php if(isset($_SESSION[_LOGINUSER])) { ?>
            <a href="/board/write">글쓰기</a>
            <a href="/user/logout">로그아웃</a>     
        <?php } else { ?>
            <a href="/user/login">로그인</a>
            <a href="/user/join">회원가입</a>
        <?php }  ?>
        <a href="/board/list">리스트</a>
    </div>
    ------------------------------------------------------
    # mod.php
    <div>
        <h1>수정</h1>
        <form action="modProc" method="post">
            <input type="hidden" name="i_board" value="<?=$this->data->i_board?>">
            <div>제목 : <input type="text" name="title" value="<?=$this->data->title?>"></div>
            <div>내용 : <textarea name="ctnt"><?=$this->data->ctnt?></textarea></div>
            <div>
                <input type="submit" value="수정">
            </div>
        </form>
    </div>
    
