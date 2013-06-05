<?php
class tweet
{
    private $account;
    private $author;
    private $msg;
    private $img;
    private $time;
    private $id;

    public function __construct($account, $author, $msg, $img, $time, $id) {
        $this->account = $account;
        $this->author = $author;
        $this->msg = $msg;
        $this->img = $img;
        $this->time = $time;
        $this->id = $id;
    }
    public function getID()
    {
        return $this->id;
    }
}
?>
