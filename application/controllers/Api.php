<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH . 'libraries/REST_Controller.php');

class Api extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->library('session');
        $this->checklogin = $this->session->userdata('logged_in');
        $this->user_id = $this->session->userdata('logged_in')['login_id'];
    }

    public function index() {
        $this->load->view('welcome_message');
    }

    function updateCurd_post() {
        $fieldname = $this->post('name');
        $value = $this->post('value');
        $pk_id = $this->post('pk');
        $tablename = $this->post('tablename');
        if ($this->checklogin) {
            $data = array($fieldname => $value);
            $this->db->set($data);
            $this->db->where("id", $pk_id);
            $this->db->update($tablename, $data);
        }
    }

    //function for product list
    function loginOperation_get() {
        $userid = $this->user_id;
        $this->db->select('au.id,au.first_name,au.last_name,au.email,au.contact_no');
        $this->db->from('admin_users au');
        $this->db->where('id', $userid);
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row();
        $this->response($result);
    }

    //Login Function 
    //function for product list
    function loginOperation_post() {
        $email = $this->post('email');
        $password = $this->post('password');
        $this->db->select('au.id,au.first_name,au.last_name,au.email,au.contact_no');
        $this->db->from('admin_users au');
        $this->db->where('email', $email);
        $this->db->where('password', md5($password));
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row();

        $sess_data = array(
            'username' => $result->email,
            'first_name' => $result->first_name,
            'last_name' => $result->last_name,
            'login_id' => $result->id,
        );
        $this->session->set_userdata('logged_in', $sess_data);
        $this->response($result);
    }

    function registerMobileGuest_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $reg_id = $this->post('reg_id');
        $model = $this->post('model');
        $manufacturer = $this->post('manufacturer');
        $uuid = $this->post('uuid');
        $regArray = array(
            "reg_id" => $reg_id,
            "manufacturer" => $manufacturer,
            "uuid" => $uuid,
            "model" => $model,
            "user_id" => "Guest",
            "user_type" => "Guest",
            "datetime" => date("Y-m-d H:i:s a")
        );
        $this->db->where('reg_id', $reg_id);
        $query = $this->db->get('gcm_registration');
        $regarray = $query->result_array();
        if ($regarray) {
            
        } else {
            $this->db->insert('gcm_registration', $regArray);
        }
        $this->response(array("status" => "done", "regdata"=>$regArray));
    }

    //Mobile Booking APi
    function bookingFromMobile_post() {
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header('Access-Control-Allow-Origin: *');

        $this->config->load('rest', TRUE);
        $bookingarray = $this->post();
        //print_r($bookingarray);

        $web_order = array(
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'contact' => $this->post('contact_no'),
            'select_date' => $this->post('select_date'),
            'select_time' => $this->post('select_time'),
            'booking_type' => $this->post('book_type'),
            'extra_remark' => $this->post('message'),
            'select_table' => $this->post('select_table'),
            'people' => "",
            "usertype" => "Customer",
            'datetime' => date("Y-m-d H:i:s a"),
            "order_source" => "Mobile App",
            'order_date' => date("Y-m-d"),
            'status' => "0",
        );
        $this->db->insert('web_order', $web_order);

        $last_id = $this->db->insert_id();
        $oderid = $last_id;
        $ordertype = $this->post('booking_type');
        $orderlog = array(
            'log_type' => "Order Received",
            'log_datetime' => date('Y-m-d H:i:s'),
            'user_id' => "",
            'order_id' => $last_id,
            'log_detail' => "Order No. #$last_id  $ordertype From Mobile App",
        );
        $this->db->insert('system_log', $orderlog);

        $this->response(array("status" => "done"));
    }

    function registration_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $name = $this->post('name');
        $email = $this->post('email');
        $contact_no = $this->post('contact_no');
        $password = $this->post('password');
        $regArray = array(
            "name" => $name,
            "email" => $email,
            "contact_no" => $contact_no,
            "password" => $password,
            "datetime" => date("Y-m-d H:i:s a")
        );
        $this->db->where('email', $email);
        $query = $this->db->get('app_user');
        $userdata = $query->row();
        if ($userdata) {
            $this->response(array("status" => "already", "userdata" => ""));
        } else {
            $this->db->insert('app_user', $regArray);
            $this->response(array("status" => "done", "userdata" => $regArray));
        }
    }

    function loginmob_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $email = $this->post('email');
        $password = $this->post('password');
        $regArray = array(
            "email" => $email,
            "password" => $password,
        );
        $this->db->where('email', $email);
        $this->db->where('password', $password);
        $query = $this->db->get('app_user');
        $userdata = $query->row();
        if ($userdata) {
            $this->response(array("status" => "done", "userdata" => $userdata));
        } else {
            $this->response(array("status" => "error", "userdata" => ""));
        }
    }

    function updateProfile_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $email = $this->post('email');
        $profiledata = array(
            'name' => $this->post('name'),
            'email' => $this->post('email'),
            'contact_no' => $this->post('contact_no'),
        );
        $this->db->set($profiledata);
        $this->db->where('email', $email); //set column_name and value in which row need to update
        $this->db->update("app_user");
        $this->db->order_by('name asc');

        $this->db->where('email', $email); //set column_name and value in which row need to update
        $query = $this->db->get('app_user');
        $userData = $query->row();
        $this->response(array("userdata" => $userData));
    }

    //function for product list
    function userbooking_post() {
        $this->config->load('rest', TRUE);
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $email = $this->post('email');
        $this->db->order_by('id desc');
        $this->db->where('email', $email);
        $query = $this->db->get("web_order");
        $result = $query->result();
        $this->response($result);
    }

//
    //function for product list
    function services_get() {
        $services = $this->Product_model->serviceModel();
        $this->response($services);
    }

    //-----------
    //function for product list
    function services3_get() {
        $services = $this->Product_model->serviceModel3();
        $this->response($services);
    }

//-----------

    function servicesAdmin_get() {
        $services = $this->Product_model->serviceModel2();
        $this->response($services);
    }

    function sliderImages_get() {
        $query = $this->db->get("slider_images");
        $sliderimages = $query->result();
        $this->response($sliderimages);
    }

    function reviews_get() {
        $reviews = $this->Product_model->reviews();
        $this->response($reviews);
    }

    function gallery_get() {
        $query = $this->db->get("gallery_images");
        $galleryList = $query->result();
        $this->response($galleryList);
    }

}

?>