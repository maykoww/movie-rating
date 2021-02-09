<?php

class Review {
    public $id;
    public $rating;
    public $review;
    public $users_id;
    public $moview_id;
}

interface IReview {
    public function buildReview($data);
    public function create(Review $review);
    public function getReview($id);
    public function hasAlreadyReviewed($id, $user_id);
    public function getRating($id);
}