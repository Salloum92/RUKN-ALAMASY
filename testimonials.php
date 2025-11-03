<?php
// دالة لتوليد النجوم بأسماء فريدة
function generateCleanStarsCustom($rating) {
    $stars = '';
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $fullStars) {
            $stars .= '<i class="bi bi-star-fill clean-star-custom filled-custom"></i>';
        } elseif ($i == $fullStars + 1 && $hasHalfStar) {
            $stars .= '<i class="bi bi-star-half clean-star-custom filled-custom"></i>';
        } else {
            $stars .= '<i class="bi bi-star clean-star-custom"></i>';
        }
    }
    return $stars;
}
?>