<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$type = $this->session->userdata('type');
		if($type != 'A')
		{
			$this->session->set_flashdata('no_access', 'You are not allowed or not logged in! Please Log in with an admin account');
			redirect('users/login');
		}
	}

/*=============== Admin Index Page =================*/
	public function index()
	{
		$view['admin_view'] = "admin/admin_index";
		$this->load->view('layouts/admin_layout', $view);
	}
/*======================================================
					CATEGORY
========================================================*/

/*============= Category && Category List Page =========*/
	public function category()
	{
		$this->load->model('admin_model');
		$view['category'] = $this->admin_model->get_category();

		$view['admin_view'] = "admin/category";
		$this->load->view('layouts/admin_layout', $view);
	}

/*============ Category Create page =====================*/
	public function add_category()
	{
		$this->form_validation->set_rules('category', 'Category name', 'trim|required|alpha_numeric_spaces');
		$this->form_validation->set_rules('description', 'Description', 'trim|required|strip_tags[description]');

		if($this->form_validation->run() == FALSE)
		{
			$view['admin_view'] = "admin/add_category";
			$this->load->view('layouts/admin_layout', $view);
		}
		else
		{
			$this->load->model('admin_model');
			if($this->admin_model->create_category())
			{
				$this->session->set_flashdata('success', 'Category created successfully');
				redirect('admin/category');
			}
			else
			{
				print $this->db->error();
			}
		}
		
	}

/*================ Category Detail display page ================*/
	public function ctg_view($id)
	{
		$this->load->model('admin_model');
		$view['ctg_detail'] = $this->admin_model->get_ctg_detail($id);

		$view['admin_view'] = "admin/ctg_view";
		$this->load->view('layouts/admin_layout', $view);

	}

/*================ Category Edit || Update ================*/
	public function ctg_edit($id)
	{
		/* For geting the existing info...*/
		$this->load->model('admin_model');
		$view['ctg_detail'] = $this->admin_model->get_ctg_detail($id);

		$this->form_validation->set_rules('category', 'Category name', 'trim|required|alpha_numeric_spaces');
		$this->form_validation->set_rules('description', 'Description', 'trim|required|callback_my_rules');
		

		if($this->form_validation->run() == FALSE)
		{
			$view['admin_view'] = "admin/ctg_edit";
			$this->load->view('layouts/admin_layout', $view);
		}
		else
		{
			$this->load->model('admin_model');
			if($this->admin_model->edit_category($id, $data))
			{
				$this->session->set_flashdata('success', 'Category Updated successfully');
				redirect('admin/category');
			}
			else
			{
				print $this->db->error();
			}
		}
	}

/*=============== Delete Category =================*/
	public function ctg_delete($id)
	{
		$this->load->model('admin_model');
		$this->admin_model->delete_category($id);
		
		$this->session->set_flashdata('success', '<i class= "fas fa-trash text-danger"></i> Category deleted successfully');
		redirect('admin/category');
	}



/*==================================================
					USERS
====================================================*/

/*============= Display all Users ================*/
	public function allUsers()
	{
		$this->load->model('admin_model');
		$view['users_data'] = $this->admin_model->get_users();

		$view['admin_view'] = "admin/view_users";
		$this->load->view('layouts/admin_layout', $view);
	}
/*=============== ADD Users By admin ===============*/
	public function add_users()
	{
		$this->form_validation->set_rules('name', 'Name', 'trim|required|alpha_numeric_spaces');
		$this->form_validation->set_rules('contact', 'Contact', 'trim|required|numeric');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|alpha_dash|min_length[3]');
		$this->form_validation->set_rules('repassword', 'Confirm Password',
		'trim|required|alpha_dash|min_length[3]|matches[password]');
		$this->form_validation->set_rules('type', 'Type','trim|required');
		$this->form_validation->set_rules('address', 'Address', 'trim|required|max_length[80]|strip_tags[address]');
		$this->form_validation->set_rules('city', 'City', 'trim|required|alpha_numeric_spaces');


		if($this->form_validation->run() == FALSE)
		{
			$view['admin_view'] = "admin/add_users";
			$this->load->view('layouts/admin_layout', $view);
		}
		else
		{
			$this->load->model('admin_model');

			if($this->admin_model->add_user())
			{
				$this->session->set_flashdata('success', 'Your Registration is successfull');
				redirect('admin/allUsers');
			}
			else
			{
				print $this->db->error();
			}

		}
	}
/*=============== Delete User =================*/
	public function user_delete($id)
	{
		$this->load->model('admin_model');
		$this->admin_model->delete_user($id);
		
		$this->session->set_flashdata('success', '<i class= "fas fa-trash text-danger"></i> User deleted successfully');
		redirect('admin/allUsers');
	}


/*=============================================
					BOOKS
===============================================*/
/*================ Books &&  All Books list page ===============*/
	public function books()
	{
		$this->load->model('admin_model');
		$view['books'] = $this->admin_model->get_books();

		$view['admin_view'] = "admin/books";
		$this->load->view('layouts/admin_layout', $view);
	}

/*================ Add Books Page =================*/
	public function add_books()
	{
		/*=== LOAD DYNAMIC CATAGORY ===*/
		$this->load->model('admin_model');
		$view['category'] = $this->admin_model->get_category();
		/*==============================*/

		/*==== Image Upload validation*/
		$config = [
			'upload_path'=>'./uploads/image/',
			'allowed_types'=>'jpg|png',
			'max_size' => '400,',
			'overwrite' => FALSE
			];

		$this->load->library('upload', $config);

		$this->form_validation->set_rules('book_name', 'Book name', 'trim|required|strip_tags[book_name]');
		$this->form_validation->set_rules('description', 'Description', 'trim|required|min_length[100]|strip_tags[description]');
		$this->form_validation->set_rules('author', 'Author name', 'trim|required|alpha_numeric_spaces|strip_tags[author]');
		$this->form_validation->set_rules('publisher', 'Publisher name', 'trim|required|alpha_numeric_spaces|strip_tags[publisher]');
		$this->form_validation->set_rules('price', 'Price', 'trim|required|alpha_numeric_spaces|strip_tags[price]');
		$this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|numeric|strip_tags[quantity]');
		$this->form_validation->set_rules('categoryId', 'Category', 'trim|required');
		/*$this->form_validation->set_rules('userfile', 'File', 'trim|required');*/


		if(($this->form_validation->run() && $this->upload->do_upload()) == FALSE)
		{
			
			$view['admin_view'] = "admin/add_books";
			$this->load->view('layouts/admin_layout', $view);

		}
		else
		{
			$this->load->model('admin_model');

			if($this->admin_model->add_books())
			{
				$this->session->set_flashdata('success', 'Book added successfully');
				redirect('admin/books');
			}
			else
			{
				print $this->db->error();
			}

		}
	}

	public function book_view($id)
	{
		$this->load->model('admin_model');
		$view['book_detail'] = $this->admin_model->get_book_detail($id);

		$view['admin_view'] = "admin/book_view";
		$this->load->view('layouts/admin_layout', $view);
	}

	public function book_edit($id)
	{
		/*=== LOAD DYNAMIC CATAGORY ===*/
		$this->load->model('admin_model');
		$view['category'] = $this->admin_model->get_category();
		/*==============================*/
		/* For geting the existing info...*/
		$this->load->model('admin_model');
		$view['book_detail'] = $this->admin_model->get_book_detail($id);

		/*==== Image Upload validation*/
		$config = [
			'upload_path'=>'./uploads/image/',
			'allowed_types'=>'jpg|png',
			'max_size' => '400,',
			'overwrite' => FALSE
			];

		$this->load->library('upload', $config);

		$this->form_validation->set_rules('book_name', 'Book name', 'trim|required|alpha_numeric_spaces|strip_tags[book_name]');
		$this->form_validation->set_rules('description', 'Description', 'trim|required|min_length[100]|strip_tags[description]');
		$this->form_validation->set_rules('author', 'Author name', 'trim|required|alpha_numeric_spaces|strip_tags[author]');
		$this->form_validation->set_rules('publisher', 'Publisher name', 'trim|required|alpha_numeric_spaces|strip_tags[publisher]');
		$this->form_validation->set_rules('price', 'Price', 'trim|required|alpha_numeric_spaces|strip_tags[price]');
		$this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|numeric|strip_tags[quantity]');
		$this->form_validation->set_rules('categoryId', 'Category', 'trim|required');
		/*$this->form_validation->set_rules('userfile', 'File', 'trim|required');*/


		if(($this->form_validation->run() && $this->upload->do_upload()) == FALSE)
		{
			
			$view['admin_view'] = "admin/book_edit";
			$this->load->view('layouts/admin_layout', $view);

		}
		else
		{
			$this->load->model('admin_model');

			if($this->admin_model->edit_book($id, $data))
			{
				$this->session->set_flashdata('success', 'Book info update successfully');
				redirect('admin/books');
			}
			else
			{
				print $this->db->error();
			}

		}
	}

	public function book_delete($id)
	{
		$this->load->model('admin_model');
		$this->admin_model->delete_book($id);
		
		$this->session->set_flashdata('success', '<i class= "fas fa-trash text-danger"></i> Book deleted successfully');
		redirect('admin/books');		
	}

/*============== SET CUSTOM VALIDATION RULES FOR TEXT-AREA ==============*/

	public function my_rules($description)
	{
		if(!preg_match("/^([a-zA-Z0-9.,; ])+$/i", $description))
		{
			$this->form_validation->set_message('my_rules', 'The %s field can only contains alphabet, numbers, dashes and punctuations.');
			return false;
		}
		else
		{
			return true;
		}
	}

}
