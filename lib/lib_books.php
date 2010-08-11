<?php
function get_books_list() {
    $res = sql_query("SELECT `book_id`, `book_name` FROM `books` WHERE `parent_id`=0 ORDER BY `book_name`");
    $out = array('num' => sql_num_rows($res));
    while ($r = sql_fetch_array($res)) {
       $out['list'][] = array('id' => $r['book_id'], 'title' => $r['book_name']);
    }
    return $out;
}
function get_book_page($book_id, $ext = false) {
    $r = sql_fetch_array(sql_query("SELECT * FROM `books` WHERE `book_id`=$book_id LIMIT 1"));
    $out = array (
        'id'     => $book_id,
        'title'  => $r['book_name'],
        'select' => books_get_select()
    );
    $res = sql_query("SELECT tag_name FROM book_tags WHERE book_id=$book_id");
    while ($r = sql_fetch_array($res)) {
        if (preg_match('/^(.+?)\:(.+)$/', $r['tag_name'], $matches)) {
            $out['tags'][] = array('prefix' => $matches[1], 'body' => $matches[2]);
        }
    }
    $res = sql_query("SELECT book_id, book_name FROM books WHERE parent_id=$book_id");
    while($r = sql_fetch_array($res)) {
        $out['children'][] = array('id' => $r['book_id'], 'title' => $r['book_name']);
    }
    if($ext) {
        $res = sql_query("SELECT p.`pos` ppos, s.sent_id, s.`pos` spos FROM paragraphs p LEFT JOIN sentences s ON (p.par_id = s.par_id) WHERE p.book_id = $book_id ORDER BY p.`pos`, s.`pos`");
        while ($r = sql_fetch_array($res)) {
            $snippet = '';
            $res1 = sql_query("SELECT `tf_text` AS txt FROM `text_forms` WHERE `sent_id` = ".$r['sent_id']." ORDER BY `pos` LIMIT 3", 0);
            while ($r1 = sql_fetch_array($res1)) {
                $snippet .= $r1['txt'].' ';
            }
            if ($snippet) $snippet .= '...';
            $res1 = sql_query("SELECT `tf_text` AS txt FROM `text_forms` WHERE `sent_id` = ".$r['sent_id']." ORDER BY `pos` DESC LIMIT 3", 0);
            $txt = '';
            while ($r1 = sql_fetch_array($res1)) {
                $txt = ' '.$r1['txt'].$txt;
            }
            $snippet .= $txt;
            $out['paragraphs'][$r['ppos']][] = array('pos' => $r['spos'], 'id' => $r['sent_id'], 'snippet' => typo_spaces($snippet));
        }
    } else {
        $res = sql_query("SELECT p.`pos`, s.sent_id FROM paragraphs p LEFT JOIN sentences s ON (p.par_id = s.par_id) WHERE p.book_id = $book_id ORDER BY p.`pos`, s.`pos`");
        while ($r = sql_fetch_array($res)) {
            $out['paragraphs'][$r['pos']][] = array('id' => $r['sent_id']);
        }
    }
    return $out;
}
function books_add($name, $parent_id=0) {
    if ($name == '') {
        die ("Название не может быть пустым.");
    }
    if (sql_query("INSERT INTO `books` VALUES(NULL, '$name', '$parent_id')")) {
        header("Location:books.php?book_id=$parent_id");
    } else {
        //some error message
    }
}
function books_move($book_id, $to_id) {
    if ($book_id == $to_id) {
        header("Location:books.php?book_id=$book_id");
        return;
    }
    if (sql_query("UPDATE `books` SET `parent_id`='$to_id' WHERE `book_id`=$book_id LIMIT 1")) {
        header("Location:books.php?book_id=$to_id");
    } else {
        //some error message
    }
}
function books_rename($book_id, $name) {
    if ($name == '') {
        die ("Название не может быть пустым.");
    }
    if (sql_query("UPDATE `books` SET `book_name`='$name' WHERE `book_id`=$book_id LIMIT 1")) {
        header("Location:books.php?book_id=$book_id");
    } else {
        //some error meassage
    }
}
function books_get_select($parent = -1) {
    $out = '';
    $pg = $parent > -1 ? "WHERE `parent_id`=$parent " : '';
    $res = sql_query("SELECT `book_id`, `book_name` FROM `books` ".$pg."ORDER BY `book_name`", 0);
    while($r = sql_fetch_array($res)) {
        $out .= "<option value='".$r['book_id']."'>".$r['book_name']."</option>";
    }
    return $out;
}
function books_add_tag($book_id, $tag_name) {
    if ($book_id && $tag_name) {
        if (!sql_query("DELETE FROM `book_tags` WHERE book_id=$book_id AND tag_name='$tag_name'") || !sql_query("INSERT INTO `book_tags` VALUES('$book_id', '$tag_name')")) {
            die("Couldn't add tag");
        }
    }
    header("Location:books.php?book_id=$book_id");
}
function books_del_tag($book_id, $tag_name) {
    if ($book_id && $tag_name) {
        if (!sql_query("DELETE FROM `book_tags` WHERE book_id=$book_id AND tag_name='$tag_name'")) {
            die("Couldn't remove tag");
        }
    }
    header("Location:books.php?book_id=$book_id");
}
?>
