<?php

class Model_Post extends \Orm\Model
{
	protected static $_properties = array(
		"id" => array(
			"label" => "Id",
			"data_type" => "int",
		),
		"category_id" => array(
			"label" => "Category id",
			"data_type" => "int",
		),
		"slug" => array(
			"label" => "Slug",
			"data_type" => "varchar",
		),
		"title" => array(
			"label" => "Title",
			"data_type" => "varchar",
		),
		"image" => array(
			"label" => "Image",
			"data_type" => "varchar",
		),
		"intro" => array(
			"label" => "Intro",
			"data_type" => "mediumtext",
		),
		"content" => array(
			"label" => "Content",
			"data_type" => "mediumtext",
		),
		"publication_date" => array(
			"label" => "Publication date",
			"data_type" => "int",
		),
		"status" => array(
			"label" => "Status",
			"data_type" => "int",
		),
		"deleted" => array(
			"label" => "Deleted",
			"data_type" => "int",
		),
		"created_at" => array(
			"label" => "Created at",
			"data_type" => "int",
		),
		"updated_at" => array(
			"label" => "Updated at",
			"data_type" => "int",
		),
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'property' => 'created_at',
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_update'),
			'property' => 'updated_at',
			'mysql_timestamp' => false,
		),
	);

	protected static $_table_name = 'posts';

	protected static $_primary_key = array('id');

	protected static $_has_many = array(
	);

	protected static $_many_many = array(
		'labels' => array(
			'key_from'         => 'id',
			'key_through_from' => 'post_id',
			'table_through'    => 'posts_labels_relations',
			'key_through_to'   => 'label_id',
			'model_to'         => 'Model_Posts_Label',
			'key_to'           => 'id',
			'cascade_save'     => false,
			'cascade_delete'   => false,
		)
	);

	protected static $_has_one = array(
	);

	protected static $_belongs_to = array(
		'category' => array(
			'key_from'       => 'category_id',
			'model_to'       => 'Model_Posts_Category',
			'key_to'         => 'id',
			'cascade_save'   => false,
			'cascade_delete' => false,
		)
	);


	/* Functions */
	public static function get_posts()
	{
		$posts_info = array();
		$per_page   = 10;
		$pagination = '';

		$posts = Model_Post::query()
		->where('publication_date', '<=', time())
		->where('status', 1)
		->where('deleted', 0);

		$config = array(
			'pagination_url' => Uri::current(),
			'total_items'    => $posts->count(),
			'per_page'       => $per_page,
			'uri_segment'    => 'pagina',
		);

		$pagination = Pagination::forge('posts', $config);

		$posts = $posts->order_by('publication_date', 'desc')
		->rows_limit($pagination->per_page)
		->rows_offset($pagination->offset)
		->get();

		if(!empty($posts))
		{
			foreach($posts as $post)
			{
				$posts_info[] = array(
					'slug'  => $post->slug,
					'title' => $post->title,
					'image' => $post->image,
					'intro' => $post->intro
				);
			}
		}

		return array(
			'posts'      => $posts_info,
			'pagination' => $pagination
		);
	}


	public static function get_posts_category($slug = '')
	{
		$posts_info    = array();
		$per_page      = 10;
		$pagination    = '';
		$category_name = '';

		$category = Model_Posts_Category::query()
		->where('slug', $slug)
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get_one();

		if(!empty($category))
		{
			$category_name = $category->name;

			$posts = Model_Post::query()
			->where('category_id', $category->id)
			->where('publication_date', '<=', time())
			->where('status', 1)
			->where('deleted', 0);

			$config = array(
				'pagination_url' => Uri::current(),
				'total_items'    => $posts->count(),
				'per_page'       => $per_page,
				'uri_segment'    => 'pagina',
			);

			$pagination = Pagination::forge('posts', $config);

			$posts = $posts->order_by('publication_date', 'desc')
			->rows_limit($pagination->per_page)
			->rows_offset($pagination->offset)
			->get();

			if(!empty($posts))
			{
				foreach($posts as $post)
				{
					$posts_info[] = array(
						'slug'  => $post->slug,
						'title' => $post->title,
						'image' => $post->image,
						'intro' => $post->intro
					);
				}
			}
		}

		return array(
			'posts'      => $posts_info,
			'pagination' => $pagination,
			'category'   => $category_name
		);
	}


	public static function get_posts_label($slug = '')
	{
		$posts_info = array();
		$per_page   = 10;
		$pagination = '';
		$label_name = '';

		$label = Model_Posts_Label::query()
		->where('slug', $slug)
		->where('deleted', 0)
		->order_by('name', 'asc')
		->get_one();

		if(!empty($label))
		{
			$label_name = $label->name;

			$posts = Model_Post::query()
			->related('labels')
			->where('labels.id', $label->id)
			->where('publication_date', '<=', time())
			->where('status', 1)
			->where('deleted', 0);

			$config = array(
				'pagination_url' => Uri::current(),
				'total_items'    => $posts->count(),
				'per_page'       => $per_page,
				'uri_segment'    => 'pagina',
			);

			$pagination = Pagination::forge('posts', $config);

			$posts = $posts->order_by('publication_date', 'desc')
			->rows_limit($pagination->per_page)
			->rows_offset($pagination->offset)
			->get();

			if(!empty($posts))
			{
				foreach($posts as $post)
				{
					$posts_info[] = array(
						'slug'  => $post->slug,
						'title' => $post->title,
						'image' => $post->image,
						'intro' => $post->intro
					);
				}
			}
		}

		return array(
			'posts'      => $posts_info,
			'pagination' => $pagination,
			'label'      => $label_name
		);
	}


	public static function get_post($slug = '')
	{
		$post_info = array();
		$labels    = array();

		$post = Model_Post::query()
		->related('labels')
		->where('slug', $slug)
		->where('publication_date', '<=', time())
		->where('status', 1)
		->where('deleted', 0)
		->order_by('labels.name', 'asc')
		->get_one();

		if(!empty($post))
		{
			if(!empty($post->labels))
			{
				foreach($post->labels as $relation)
				{
					$labels[] = array(
						'slug' => $relation->slug,
						'name' => $relation->name
					);
				}
			}

			$post_info = array(
				'slug'    => $post->slug,
				'title'   => $post->title,
				'image'   => $post->image,
				'intro'   => $post->intro,
				'content' => $post->content,
				'labels'  => $labels
			);
		}

		return $post_info;
	}


	/**
    * GET_LAST_POSTS
    *
    * OBTIENE LOS REGISTROS PARA LA PAGINA DE BLOG
    *
    * @access  public
    * @return  Array
    */
	public static function get_last_posts()
	{
		# SE INICIALIZAN LAS VARIABLES
		$posts_info = array();

		# SE BUSCA LA INFORMACION A TRAVES DEL MODELO
		$posts = Model_Post::query()
		->where('publication_date', '<=', time())
		->where('status', 1)
		->where('deleted', 0)
		->order_by('publication_date', 'desc')
		->limit(3)
		->get();

		# SI SE OBTIENE INFORMACION
		if(!empty($posts))
		{
			# SE RECORRE ELEMENTO POR ELEMENTO
			foreach($posts as $post)
			{
				# SE ALMACENA LA INFORMACION
				$posts_info[] = array(
					'slug'           => $post->slug,
					'title'          => $post->title,
					'title_truncate' => Str::truncate(strip_tags($post->title), 40),
					'image'          => $post->image
				);
			}
		}

		# SE DEVUELVE EL ARREGLO
		return $posts_info;
	}
}
