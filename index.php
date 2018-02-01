<?php
header("Content-Type: text/html;charset=utf-8");

function getData()
{
    $dbms = 'mysql';
    $host = '数据库服务器地址:端口';
    $dbName = 'WordPress数据库名';
    $user = '数据库用户';
    $pass = '数据库访问密码';
    $dsn = "$dbms:host=$host;dbname=$dbName";
    try {
        $pdo = new PDO($dsn, $user, $pass, [PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"]);
        $sql = "SELECT id, B.`name`, B.term_id, B.parent, post_title FROM blog_posts AS A LEFT JOIN( SELECT blog_term_relationships.object_id, blog_terms.term_id, blog_terms.`name`, blog_term_taxonomy.parent FROM blog_terms LEFT JOIN blog_term_relationships ON blog_terms.term_id = blog_term_relationships.term_taxonomy_id LEFT JOIN blog_term_taxonomy ON blog_terms.term_id = blog_term_taxonomy.term_id WHERE blog_terms.term_id <> 1) AS B ON A.id = B.object_id WHERE post_status = 'publish' AND post_type = 'post';";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die ("[Error] : " . $e->getMessage() . "<br/>");
    }
}

function getList($pc, $term2name, $term2url, $addr = 0)
{
    $root = $pc[$addr];
    foreach ($root as $key => $value) {
        if (array_key_exists($key, $pc)) {
            $root[$term2name[$key]] = getList($pc, $term2name, $term2url, $key);
            unset($pc[$key]);
        } else {
            $root[$term2name[$key]] = $term2url[$key];
        }
        unset($root[$key]);
    }
    return $root;
}

function processing($data)
{
    // 处理数据
    $term2name = [0 => "所有文章"];
    $parent2child = [];
    $term2url = [];
    $url2term = [];
    $url2title = [];
    for ($i = 0; $i < count($data); $i++) {
        // term_id-name table
        if (!array_key_exists($data[$i]["term_id"], $term2name)) {
            $term2name[$data[$i]["term_id"]] = $data[$i]["name"];
        }
        // parent-child table
        if ((!array_key_exists($data[$i]["parent"], $parent2child))) {
            $parent2child[$data[$i]["parent"]] = [$data[$i]["term_id"] => -1];
        } else {
            if (!in_array($data[$i]["term_id"], $parent2child[$data[$i]["parent"]])) {
                $parent2child[$data[$i]["parent"]][$data[$i]["term_id"]] = -1;
            }
        }
        // post_url-term_id table
        $url2term["https://www.shaoqunliu.cn/" . $data[$i]["id"] . ".html"] = $data[$i]["term_id"];
        // url-title table
        $url2title["https://www.shaoqunliu.cn/" . $data[$i]["id"] . ".html"] = $data[$i]["post_title"];
    }
    //
    foreach ($url2term as $key => $value) {
        if (array_key_exists($value, $term2url)) {
            array_push($term2url[$value], '<a href="' . $key . '" target="_blank">' . $url2title[$key] . '</a>');
        } else {
            $term2url[$value] = ['<a href="' . $key . '" target="_blank">' . $url2title[$key] . '</a>'];
        }
    }
    //
    return getList($parent2child, $term2name, $term2url);
}

function printData($data)
{
    echo "<ul>";
    foreach ($data as $key => $value) {
        echo "<li>";
        if (is_array($value)) {
            echo $key;
            printData($value);
        } else {
            echo $value;
        }
        echo "</li>";
    }
    echo "</ul>";
}

printData(processing(getData()));