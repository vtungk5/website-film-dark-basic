<?php

class Hash
{
    static public function make($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    static public function check($password, $hashed_password)
    {
        if (password_verify($password, $hashed_password)) {
            return true;
        } else {
            return false;
        }
    }
}

