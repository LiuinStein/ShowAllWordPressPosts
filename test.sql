SELECT
  id,
  B.`name`,
  B.term_id,
  B.parent,
  post_title
FROM
  blog_posts AS A
LEFT JOIN
  (
    SELECT
      blog_term_relationships.object_id,
      blog_terms.term_id,
      blog_terms.`name`,
      blog_term_taxonomy.parent
    FROM blog_terms
      LEFT JOIN blog_term_relationships
        ON blog_terms.term_id = blog_term_relationships.term_taxonomy_id
      LEFT JOIN blog_term_taxonomy
        ON blog_terms.term_id = blog_term_taxonomy.term_id
    WHERE blog_terms.term_id <> 1
  ) AS B ON A.id = B.object_id
WHERE post_status = 'publish' AND post_type = 'post';