<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->API_ACCESS_KEY = 'AIzaSyCwKEcXKqMZWz3jmmOMOEQvKUXD1Pi2yuY';
        // (iOS) Private key's passphrase.
        $this->passphrase = 'joashp';
        // (Windows Phone 8) The name of our push channel.
        $this->channelName = "joashp";
        $this->load->library('session');
        $this->load->model('User_model');
        $this->load->model('Product_model');
        $session_user = $this->session->userdata('logged_in');
        $this->session_user_data = $this->session->userdata('logged_in');
        if ($session_user) {
            $this->user_id = $session_user['login_id'];
        } else {
            $this->user_id = 0;
        }
    }

    public function index() {
        if ($this->user_id) {
            redirect('Admin/downloadReport ');
        } else {
            redirect('Admin/login');
        }
    }

    private function useCurl($url, $headers, $fields = null) {
        // Open connection
        $ch = curl_init();
        if ($url) {
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            if ($fields) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            }

            // Execute post
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }

            // Close connection
            curl_close($ch);

            return $result;
        }
    }

    public function android($data, $reg_id_array) {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $message = array(
            'title' => $data['title'],
            'message' => $data['message'],
            'subtitle' => '',
            'tickerText' => '',
            'msgcnt' => 1,
            'vibrate' => 1
        );

        $headers = array(
            'Authorization: key=' . $this->API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $fields = array(
            'registration_ids' => $reg_id_array,
            'data' => $message,
        );

        return $this->useCurl($url, $headers, json_encode($fields));
    }

    public function iOS($data, $devicetoken) {
        $deviceToken = $devicetoken;
        $ctx = stream_context_create();
        // ck.pem is your certificate file
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);
        // Open a connection to the APNS server
        $fp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195', $err,
                $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        // Create the payload body
        $body['aps'] = array(
            'alert' => array(
                'title' => $data['mtitle'],
                'body' => $data['mdesc'],
            ),
            'sound' => 'default'
        );
        // Encode the payload as JSON
        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        // Close the connection to the server
        fclose($fp);
        if (!$result)
            return 'Message not delivered' . PHP_EOL;
        else
            return 'Message successfully delivered' . PHP_EOL;
    }

    //login page
    //login page
    function login() {
        if ($this->user_id) {
            redirect('Admin/downloadReport ');
        }

        $data1['msg'] = "";
        $data1['countrylist'] = [];

        $link = isset($_GET['page']) ? $_GET['page'] : '';
        $data1['next_link'] = $link;

        if (isset($_POST['signIn'])) {
            $username = $this->input->post('email');
            $password = $this->input->post('password');

            $this->db->select('au.id,au.first_name,au.last_name,au.email,au.password,au.user_type, au.image');
            $this->db->from('admin_users au');
            $this->db->where('email', $username);
            $this->db->where('password', md5($password));
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $userdata = $query->result_array()[0];
                $usr = $userdata['email'];
                $pwd = $userdata['password'];
                if ($username == $usr && md5($password) == $pwd) {
                    $sess_data = array(
                        'username' => $username,
                        'first_name' => $userdata['first_name'],
                        'last_name' => $userdata['last_name'],
                        'login_id' => $userdata['id'],
                    );
                    $user_id = $userdata['id'];
                    $session_cart = $this->session->userdata('session_cart');
                    $productlist = $session_cart['products'];

                    $this->Product_model->cartOperationCustomCopy($user_id);
                    $first_name = $userdata['first_name'];
                    $last_name = $userdata['last_name'];
                    $orderlog = array(
                        'log_type' => "Login",
                        'log_datetime' => date('Y-m-d H:i:s'),
                        'user_id' => $user_id,
                        'order_id' => "",
                        'log_detail' => "$first_name $last_name Login Succesful",
                    );
                    $this->db->insert('system_log', $orderlog);

                    $this->session->set_userdata('logged_in', $sess_data);


                    redirect('Admin/downloadReport ');
                } else {
                    $data1['msg'] = 'Invalid Email Or Password, Please Try Again';
                }
            } else {
                $data1['msg'] = 'Invalid Email Or Password, Please Try Again';
                redirect('Admin/login', $data1);
            }
        }



        $this->load->view('Admin/login', $data1);
    }

    // Logout from admin page
    function logout() {
        $newdata = array(
            'username' => '',
            'password' => '',
            'logged_in' => FALSE,
        );

        $first_name = $this->session_user_data['first_name'];
        $last_name = $this->session_user_data['last_name'];
        $orderlog = array(
            'log_type' => "Logout",
            'log_datetime' => date('Y-m-d H:i:s'),
            'user_id' => $this->user_id,
            'order_id' => "",
            'log_detail' => "$first_name $last_name Logout Succesful",
        );

        $this->db->insert('system_log', $orderlog);
        $this->session->unset_userdata($newdata);
        $this->session->sess_destroy();

        redirect('Account/login');
    }

    //orders list
    function dashboard() {
        if ($this->user_id == 0) {
            redirect('Admin/login');
        }

        $this->db->order_by('id desc');
        $query = $this->db->get('web_order');
        $bookinglist = $query->result();

        $data = [];
        $data['bookinglist'] = $bookinglist;
        $this->load->view('Admin/dashboard', $data);
    }

    function bookingDelete($id) {
        $this->db->where('id', $id); //set column_name and value in which row need to update
        $this->db->delete("web_order");
        redirect("Admin/bookingReport");
    }

    function error404() {
        echo "No Page Found.";
    }

    //orders list
    function downloadReport() {
        if ($this->user_id == 0) {
            redirect('Admin/login');
        }
        $this->db->order_by('id desc');
        $query = $this->db->get('gcm_registration');
        $bookinglist = $query->result();
        $data = [];
        $data['bookinglist'] = $bookinglist;
        $this->load->view('Admin/bookingReport', $data);
    }

    function sendNotification() {


        $reglist = [];
        $this->db->order_by('id desc');
        $query = $this->db->get('gcm_registration');
        $reglist2 = $query->result();

        foreach ($reglist2 as $key => $value) {
            array_push($reglist, $value['reg_id']);
        }

        $data = [];
        if (isset($_POST['deleteService'])) {
            $id = $this->input->post("service_id");
            $this->db->where('id', $id); //set column_name and value in which row need to update
            $this->db->delete("category_items");
            redirect("Admin/services");
        }
        if (isset($_POST['add_data'])) {

            $insertArray = array(
                "title" => $this->input->post("title"),
                "message" => $this->input->post("message"),
                "image" => "",
                "datetime" => date('Y-m-d H:i:s')
            );
            $this->db->insert("notification", $insertArray);
            $insert_id = $this->db->insert_id();
            $realfilename = $this->input->post("file_real_name");
            if ($realfilename) {
                $config['upload_path'] = 'assets/serviceimage';
                $config['allowed_types'] = '*';
                $tempfilename = rand(10000, 1000000);
                $tempfilename = "" . $tempfilename . $tableid;
                $ext2 = explode('.', $_FILES['file']['name']);
                $ext3 = strtolower(end($ext2));
                $ext22 = explode('.', $tempfilename);
                $ext33 = strtolower(end($ext22));
                $filename = $ext22[0];
                $file_newname = $filename . '.' . $ext3;
                $config['file_name'] = $file_newname;
                //Load upload library and initialize configuration
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('file')) {
                    $uploadData = $this->upload->data();

                    $file_newname = $uploadData['file_name'];

                    $this->db->set('image', $file_newname);
                    $this->db->where('id', $insert_id); //set column_name and value in which row need to update
                    $this->db->update("notification"); //
                }
            }
            $insertArray = array(
                "title" => $this->input->post("title"),
                "message" => $this->input->post("message"),
                "image" => $file_newname,
                "datetime" => date('Y-m-d H:i:s')
            );
            $this->android($insertArray, $reglist);

            redirect("Admin/sendNotification");
        }
        $this->db->order_by('id desc');
        $query = $this->db->get('notification');
        $notificationlist = $query->result_array();
        $data['notificationlist'] = $notificationlist;
        $this->load->view('Admin/services', $data);
    }

}

?>
