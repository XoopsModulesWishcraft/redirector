CREATE TABLE `redirections` (                          
                     `id` INT(12) UNSIGNED NOT NULL AUTO_INCREMENT,            
                     `name` VARCHAR(128) DEFAULT NULL,                         
                     `type` ENUM('302','301','iframe','header') DEFAULT NULL,  
                     `groups` VARCHAR(255) DEFAULT NULL,                       
                     `redirect_url` VARCHAR(255) DEFAULT NULL,                 
                     `redirect_message` VARCHAR(255) DEFAULT NULL,             
                     `redirect_time` INT(5) DEFAULT '3',                       
                     `agents` MEDIUMTEXT,                                      
                     `domains` MEDIUMTEXT,                                     
                     `xml_conf` MEDIUMTEXT,                                    
                     PRIMARY KEY (`id`)                                        
                   ) ENGINE=MYISAM DEFAULT CHARSET=utf8;