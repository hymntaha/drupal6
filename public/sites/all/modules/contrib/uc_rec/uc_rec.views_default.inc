<?php

/**
 * Implements hook_views_default_views().
 */
function uc_rec_views_default_views() {
  return array(
    'uc_rec_similar_products' => recommender_default_view_template(
      'uc_rec_similar_products',
      'UC Products Recommender: similar products',
      'Display on a product page "users who have ordered this product also ordered"',
      'recommender_similarity',
      'node',
      'title',
      'node',
      'uc_rec_i2i'
    ),
    'uc_rec_personal_recommendations' => recommender_default_view_template(
      'uc_rec_personal_recommendations',
      'UC Products Recommender: personal recommendations',
      'Recommend to the current user other products this user might be interested based on the users purchasing history',
      'recommender_prediction',
      'node',
      'title',
      'current_user',
      'uc_rec_i2i'
    ),
  );
}