# WEB_CORE
## To secure website must check code from this files.
- htaccess - security of file access 
- global_func.php - user input sanitizing and prevent XSS Attack
- connect.php - sanitize MYSQL query
- (include your own CSRF Token and Session Handling) - its important, ## REQUIRED (Pagchineck ko system nyo)
- All upload folder must be protected using php and htaccess

```php
## roles variable

#ADMIN
if (isset($login) && isset($role)) {
	if ($login == "success" && $role == "Admin") {
		header('Location: index.php');
		exit();
	}
#FACULTY
	if ($login == "success" && $role == "Faculty") {
		header('Location: index.php');
		exit();
	}
}


### SESSION 
$session_class->setValue('login', 'success');
$session_class->setValue('user_id', $row['id']);
$session_class->setValue('name', $row['faculty_name']);
$session_class->setValue('role', $row['user_type']);
$session_class->setValue('faculty_id', $row['faculty_id']);
$session_class->setValue('session_sched', $row['id']);
$session_class->setValue('session_title', $row['title']);

### CONSTANT variable
$user_id ==> variable for user_id
$name ==>  user name
BASE_URL ==> constant for base url (https://localhost
DOMAIN_PATH ==> access folder absolute path
```

## Installation

Copy or Clone this project.

Import the database (optional)

### Login
- Username: admin
- Password: ituser

## For Design and JS > but yu can use your own design (CSS Template)
- Use bootstrap 5+
- jQuery

## File Folder Structures
- app                 - contain php script for login users
  - global            - all base (sidebar, topheader, all inlcude js and css)
- call_func           - contain global function and connection
   - cl_session.php   - session manager with csrf token.
   - global_func.php  - all global function that does need database connection
   - connect.php      - global function for database connection
   - islogin.php      - put all your logic if user is successfully login
- config              - contain configuration files for system
- error_page          - error page to be use when encounter 404 or other error
- assets              - contain fonts images js css
    - css                 - all css files
    - fonts               - fonts file
    - images              - all image used in design
    - js                  - js folder 
- .htaccess         - any .htaccess is for security filtering



### EXAMPLE PHP CODE:
- [var_html] function - use to sanitize database data to output in user page <for string variable>
- [array_html] function - use to sanitize database data array to output in user page <for array>
  [js_clean] function - use to sanitize database data to output in user page <for variable string, and if your data is html  or from text editor>
  [js_clean_array] function - use to sanitize database data to output in user page <for array string, and if your data is html  or from text editor>

 - [csrf token] - check the example in the given link
 - [session] - check the example in the given link
  ### [php-csrf v1.0.2] https://github.com/GramThanos/php-csrf reference
  ### [Asdfdotdev] https://github.com/asdfdotdev/session/tree/main/_examples reference
  
```php


##############################################
## access session
//  Set New Value
$session_class->setValue('login', 'success');
$session_class->setValue('user_id', $row['id']);
$session_class->setValue('name', $row['faculty_name']);
$session_class->setValue('role', $row['user_type']);
$session_class->setValue('faculty_id', $row['faculty_id']);
$session_class->setValue('session_sched', $row['id']);
$session_class->setValue('session_title', $row['title']);
 
//  Get Stored Value
$login = $session_class->getValue('login');
$name = $session_class->getValue('name');
$role = $session_class->getValue('role');
$user_id = $session_class->getValue('user_id');
$session_sched = $session_class->getValue('session_sched');
$session_title = $session_class->getValue('session_title');
$faculty_id = $session_class->getValue('faculty_id');
  
```

- [output] function - use to convert array to JSON enocode
```php
  Disclaimer there's no test done for this example but this is the expected.
 ####################################
$header_code = array();
$header_code['first_section'] =  array('one'=>1);
$header_code['second_section'] =  ['second_section'=>['second_section'=>['two' => '1 associated']]];
$header_json = output($header_code); //convert array to json array
  
output: $header_json 
[
  'first_section' => [
    'one' => '1',
  ],
  'second_section' => [
    'second_section' => [
      'two' => '1 associated',
    ],
  ],
]
#################################################
  
##Example mysql query
```php
  
$sql_conds = (empty($sql_where)) ? '' : 'WHERE '.$sql_where; // ichange based sa need
$default_query ="SELECT ".$field_query." FROM ".$table_name."  ".$sql_conds."  ORDER BY ".$orderby;
$limit=" LIMIT ". $start_no.",".$query_limit; 
$sql_limit=$default_query.' '.$limit;

if($query = mysqli_query($db_connect,$sql_limit)){
	if($num = mysqli_num_rows($query)){
		while($data = mysqli_fetch_assoc($query)){
            $data['faculty_name'] = $data['faculty_name'];
			$data = array_html($data);
			$to_encode[] = $data;
		}
	}
	$output = json_encode(["last_page"=>$pages, "data"=>$to_encode,"total_record"=>$total_query]);
}else{
	$output =  json_encode(["last_page"=>0, "data"=>"","total_record"=>0]);
}

echo $output; //output
```
