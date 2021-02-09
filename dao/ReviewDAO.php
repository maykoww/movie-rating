<?php

require_once("models/Message.php");
require_once("models/Review.php");
require_once("dao/UserDAO.php");

class ReviewDAO implements IReview
{
    private $conn;
    private $url;

    public function __construct($conn, $url)
    {
        $this->conn = $conn;
        $this->url = $url;
        $this->message = new Message($url);
    }

    public function buildReview($data)
    {
        $reviewObject = new Review();

        $reviewObject->id = $data["id"];
        $reviewObject->rating = $data["rating"];
        $reviewObject->review = $data["review"];
        $reviewObject->users_id = $data["users_id"];
        $reviewObject->movies_id = $data["movies_id"];

        return $reviewObject;
    }

    public function create(Review $review)
    {
        $query = "INSERT INTO reviews (
            rating,
            review,
            movies_id,
            users_id
        ) VALUES (
            :rating,
            :review,
            :movies_id,
            :users_id
        )";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":rating", $review->rating);
        $stmt->bindParam(":review", $review->review);
        $stmt->bindParam(":movies_id", $review->movies_id);
        $stmt->bindParam(":users_id", $review->users_id);

        $stmt->execute();

        $this->message->setMessage("Review adicionada com sucesso !", "success", "index.php");
    }

    public function getReview($id)
    {
        $reviews = [];

        $query = "SELECT * FROM reviews WHERE movies_id = :movies_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":movies_id", $id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $reviewsData = $stmt->fetchAll();

            $userDao = new UserDAO($this->conn, $this->url);

            foreach ($reviewsData as $review) {
                $reviewObject = $this->buildReview($review);

                $user = $userDao->findById($reviewObject->users_id);

                $reviewObject->user = $user;

                $reviews[] = $reviewObject;
            }
        }

        return $reviews;
    }

    public function hasAlreadyReviewed($id, $user_id)
    {
        $query = "SELECT * FROM reviews WHERE movies_id = :movies_id AND users_id = :users_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":movies_id", $id);
        $stmt->bindParam(":users_id", $user_id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getRating($id)
    {
        $query = "SELECT * FROM reviews WHERE movies_id = :movies_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":movies_id", $id);

        $stmt->execute();

        $rating = 0;

        if ($stmt->rowCount() > 0) {

            $reviews = $stmt->fetchALl();

            foreach($reviews as $review) {
                $rating += $review["rating"];
            }

            $rating = $rating / count($reviews);

        } else {
            $rating = "Não avaliado";
        }

        return $rating;
    }
}
