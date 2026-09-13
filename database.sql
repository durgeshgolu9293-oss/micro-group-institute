-- Micro Group of Computer Institute Database Schema
-- Database: micro_group_institute

CREATE DATABASE IF NOT EXISTS `micro_group_institute` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `micro_group_institute`;

-- --------------------------------------------------------
-- Table: settings
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `key_name` VARCHAR(100) NOT NULL UNIQUE,
  `key_value` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`key_name`, `key_value`) VALUES
('institute_name', 'Micro Group of Computer Institute'),
('short_name', 'MGI'),
('tagline', 'Learn • Practice • Test • Achieve'),
('manager_name', 'DK Singh'),
('location', 'Bhoopganj Payagpur'),
('branch_address', 'Main Market, Bhoopganj Payagpur / Fukganj, Uttar Pradesh'),
('phone', '+91 9792686570'),
('email', 'Deep2180411008@gmail.com'),
('alt_email', 'support@microgroupinstitute.com'),
('default_passing_percentage', '40'),
('certificate_prefix', 'MGI-2026-'),
('hero_title', 'Empowering Students With Digital Skills'),
('hero_subtitle', 'Learn computer skills with quality education, digital study material, online examinations and recognized course completion certificates.')
ON DUPLICATE KEY UPDATE `key_value`=VALUES(`key_value`);

-- --------------------------------------------------------
-- Table: admins
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('superadmin', 'admin') DEFAULT 'admin',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Admin (Deep2180411008@gmail.com / Verna@325901)
INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role`, `status`) VALUES
(1, 'DK Singh (Director/Manager)', 'Deep2180411008@gmail.com', '$2y$10$ax34S6hAmGlkVINsTqJQ4.eiVbfPMpbCup59MaEU.6nOn1klDTyXC', 'superadmin', 'active')
ON DUPLICATE KEY UPDATE `email`=VALUES(`email`);

-- --------------------------------------------------------
-- Table: courses
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_name` VARCHAR(200) NOT NULL,
  `short_name` VARCHAR(50) NOT NULL,
  `description` TEXT NOT NULL,
  `duration` VARCHAR(100) NOT NULL,
  `fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `admission_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `discount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `final_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `eligibility` VARCHAR(150) NOT NULL DEFAULT '10th / 12th Pass',
  `certificate_available` TINYINT(1) DEFAULT 1,
  `course_image` VARCHAR(255) DEFAULT 'adca.svg',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `courses` (`id`, `course_name`, `short_name`, `description`, `duration`, `fee`, `admission_fee`, `discount`, `final_fee`, `eligibility`, `certificate_available`, `course_image`, `status`) VALUES
(1, 'Advanced Diploma in Computer Applications', 'ADCA', 'A comprehensive 1-year career-oriented diploma covering computer fundamentals, office automation, web designing (HTML/CSS/JS), graphic design, accounting, and database management.', '12 Months', 12000.00, 500.00, 2500.00, 10000.00, '10th / 12th Pass or Equivalent', 1, 'adca.svg', 'active'),
(2, 'Course on Computer Concepts', 'CCC', 'Government recognized IT literacy program teaching operating systems, Internet technologies, digital financial tools, cyber security, and office productivity software.', '3 Months', 3500.00, 300.00, 800.00, 3000.00, 'Open to all (No minimum schooling)', 1, 'ccc.svg', 'active'),
(3, 'Diploma in Computer Applications', 'DCA', 'Fundamental six-month diploma focusing on office tools, database essentials, internet communications, and desktop publishing for office assistants and data operators.', '6 Months', 6500.00, 400.00, 1400.00, 5500.00, '10th Pass', 1, 'dca.svg', 'active'),
(4, 'Post Graduate Diploma in Computer Applications', 'PGDCA', 'Advanced technical diploma for graduates covering computer programming, database architecture, software engineering, and systems administration.', '12 Months', 15000.00, 600.00, 3100.00, 12500.00, 'Graduation in any stream', 1, 'pgdca.svg', 'active'),
(5, 'Tally Prime with GST & E-Way Bill', 'Tally Prime', 'Professional computerized accounting course with practical business accounting, GST invoicing, inventory control, payroll, and TDS reconciliation.', '3 Months', 5000.00, 300.00, 1000.00, 4300.00, '10th/12th / Commerce Preferred', 1, 'tally.svg', 'active')
ON DUPLICATE KEY UPDATE `course_name`=VALUES(`course_name`);

-- --------------------------------------------------------
-- Table: subjects
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_id` INT NOT NULL,
  `subject_name` VARCHAR(150) NOT NULL,
  `subject_code` VARCHAR(50) DEFAULT NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `subjects` (`id`, `course_id`, `subject_name`, `subject_code`, `description`) VALUES
(1, 1, 'Computer Fundamentals & OS', 'ADCA-101', 'Hardware architecture, memory concepts, Windows and command line fundamentals'),
(2, 1, 'MS Office Productivity Suite', 'ADCA-102', 'MS Word, MS Excel, MS PowerPoint, and MS Access with advanced formulas and automation'),
(3, 1, 'Internet & Web Technologies', 'ADCA-103', 'HTML5, CSS3, JavaScript basics, web browsing, email protocols, and cyber awareness'),
(4, 1, 'Database Management & SQL', 'ADCA-104', 'Relational database concepts, queries, normalization, and MySQL fundamentals'),
(5, 1, 'Desktop Publishing & Graphics', 'ADCA-105', 'Photoshop, CorelDRAW, page layout, and digital design fundamentals'),
(6, 2, 'Introduction to Computer & GUI', 'CCC-01', 'Basics of hardware, software, mouse/keyboard handling, Windows desktop GUI'),
(7, 2, 'Word Processing & Spreadsheets', 'CCC-02', 'Document formatting, tables, spreadsheet formulas, charts, and presentations'),
(8, 2, 'Internet, WWW & Web Browsers', 'CCC-03', 'LAN/WAN, Search engines, emailing, social networking, and cloud services'),
(9, 2, 'Digital Financial Services & Cyber Security', 'CCC-04', 'UPI, AEPS, Net Banking, cards, OTP safety, phishing defense, and IT Act overview')
ON DUPLICATE KEY UPDATE `subject_name`=VALUES(`subject_name`);

-- --------------------------------------------------------
-- Table: students
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `roll_number` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `mobile` VARCHAR(20) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `course_id` INT NOT NULL,
  `admission_date` DATE NOT NULL,
  `status` ENUM('active', 'inactive', 'completed') DEFAULT 'active',
  `profile_image` VARCHAR(255) DEFAULT 'default_avatar.svg',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Sample Students (Password: student123)
-- Password hash for 'student123'
INSERT INTO `students` (`id`, `roll_number`, `name`, `email`, `mobile`, `password`, `course_id`, `admission_date`, `status`) VALUES
(1, 'MG202601', 'Rahul Kumar', 'rahul@gmail.com', '9876543211', '$2y$10$wK1VqJz0H9GzE7jM1jU18.Q69H.K3oQpE0FzOq6n0sJzTfR2V1G2W', 1, '2026-01-10', 'active'),
(2, 'MG202602', 'Pooja Verma', 'pooja@gmail.com', '9876543212', '$2y$10$wK1VqJz0H9GzE7jM1jU18.Q69H.K3oQpE0FzOq6n0sJzTfR2V1G2W', 2, '2026-02-01', 'active'),
(3, 'MG202603', 'Amit Sharma', 'amit@gmail.com', '9876543213', '$2y$10$wK1VqJz0H9GzE7jM1jU18.Q69H.K3oQpE0FzOq6n0sJzTfR2V1G2W', 1, '2026-02-15', 'active')
ON DUPLICATE KEY UPDATE `roll_number`=VALUES(`roll_number`);

-- --------------------------------------------------------
-- Table: notes
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `notes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  `chapter_name` VARCHAR(150) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(20) DEFAULT 'pdf',
  `file_size` VARCHAR(50) DEFAULT '1.2 MB',
  `description` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `notes` (`id`, `course_id`, `subject_id`, `chapter_name`, `title`, `file_path`, `file_type`, `file_size`, `description`) VALUES
(1, 1, 1, 'Chapter 1: Hardware & Architecture', 'Complete Computer Fundamentals Guide', 'adca_ch1_fundamentals.pdf', 'pdf', '2.4 MB', 'In-depth notes on CPU architecture, motherboard components, RAM/ROM, storage, and I/O devices.'),
(2, 1, 2, 'Chapter 2: Microsoft Word Mastery', 'MS Word 2021 Complete Handout with Shortcuts', 'adca_ch2_msword.pdf', 'pdf', '3.1 MB', 'Document formatting, mail merge, tables, macros, and standard keyboard shortcuts.'),
(3, 1, 2, 'Chapter 3: Advanced Excel Formulas', 'Excel Formulas, Charts & VLOOKUP Reference', 'adca_ch3_msexcel.pdf', 'pdf', '4.5 MB', 'Formulas (VLOOKUP, XLOOKUP, INDEX/MATCH), Pivot Tables, Conditional Formatting, and Data Analysis.'),
(4, 1, 3, 'Chapter 4: HTML5 & CSS3 Web Designing', 'Web Development Starter Guide with Live Examples', 'adca_ch4_webdesign.pdf', 'pdf', '2.8 MB', 'Semantic HTML elements, CSS Box Model, Flexbox, Grid, and responsive styling techniques.'),
(5, 2, 6, 'Chapter 1: Intro to Operating Systems', 'CCC Module 1 - Windows & Linux Desktop Notes', 'ccc_ch1_os.pdf', 'pdf', '1.8 MB', 'Files, directories, control panel settings, shortcuts, and GUI operations for CCC syllabus.'),
(6, 2, 8, 'Chapter 3: Internet & Email Services', 'CCC Module 3 - Networking & Web Protocols', 'ccc_ch3_internet.pdf', 'pdf', '2.1 MB', 'IP addressing, DNS, browsers, search techniques, and safe emailing procedures.'),
(7, 2, 9, 'Chapter 4: Digital Payments & Cyber Security', 'CCC Module 4 - UPI, Net Banking & Safety Guide', 'ccc_ch4_digital_finance.pdf', 'pdf', '1.9 MB', 'Complete handbook on BHIM UPI, IMPS, RTGS, NEFT, cyber hygiene, and password protection.')
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- --------------------------------------------------------
-- Table: exams
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `exams` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_id` INT NOT NULL,
  `subject_id` INT DEFAULT NULL,
  `exam_title` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `total_questions` INT NOT NULL DEFAULT 10,
  `duration_minutes` INT NOT NULL DEFAULT 15,
  `max_marks` INT NOT NULL DEFAULT 50,
  `passing_percentage` INT NOT NULL DEFAULT 40,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `exams` (`id`, `course_id`, `subject_id`, `exam_title`, `description`, `total_questions`, `duration_minutes`, `max_marks`, `passing_percentage`, `status`) VALUES
(1, 1, 1, 'ADCA Comprehensive Assessment Exam', 'Official course completion online test for Advanced Diploma in Computer Applications covering Office, Web, and Database.', 10, 15, 50, 40, 'active'),
(2, 2, 6, 'CCC Official Certification Mock Test', 'Standard examination covering Computer Concepts, LibreOffice/MS Office, Internet & Digital Finance.', 10, 15, 50, 40, 'active'),
(3, 1, 2, 'MS Office Suite Skill Evaluation', 'Specialized test assessing practical mastery over MS Word, Excel, and PowerPoint tools.', 10, 15, 50, 40, 'active')
ON DUPLICATE KEY UPDATE `exam_title`=VALUES(`exam_title`);

-- --------------------------------------------------------
-- Table: questions
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `questions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `exam_id` INT NOT NULL,
  `question_text` TEXT NOT NULL,
  `option_a` TEXT NOT NULL,
  `option_b` TEXT NOT NULL,
  `option_c` TEXT NOT NULL,
  `option_d` TEXT NOT NULL,
  `correct_option` ENUM('A', 'B', 'C', 'D') NOT NULL,
  `marks` INT NOT NULL DEFAULT 5,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `questions` (`id`, `exam_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `marks`) VALUES
(1, 1, 'Which of the following is considered the primary brain of the computer system?', 'RAM', 'Central Processing Unit (CPU)', 'Hard Disk', 'Motherboard', 'B', 5),
(2, 1, 'What is the default file extension of a modern Microsoft Word document?', '.txt', '.docm', '.docx', '.xlsx', 'C', 5),
(3, 1, 'In Microsoft Excel, which function is used to find the highest number in a selected range of cells?', '=COUNT()', '=MAX()', '=TOP()', '=HIGH()', 'B', 5),
(4, 1, 'Which HTML tag is used to create an interactive hyperlink on a web page?', '<link>', '<a>', '<href>', '<url>', 'B', 5),
(5, 1, 'What does the abbreviation \"SQL\" stand for in database management?', 'Structured Question Language', 'Standard Quality List', 'Structured Query Language', 'Simple Query Logic', 'C', 5),
(6, 1, 'Which protocol is standardly used for securely browsing web pages over the Internet?', 'FTP', 'HTTP', 'HTTPS', 'SMTP', 'C', 5),
(7, 1, 'In Microsoft PowerPoint, which key is pressed to begin a slide show presentation from the first slide?', 'F2', 'F5', 'F7', 'F12', 'B', 5),
(8, 1, 'Which of the following is an example of non-volatile primary computer memory?', 'RAM', 'Cache', 'ROM', 'Virtual Memory', 'C', 5),
(9, 1, 'What CSS property is used to change the background color of an HTML element?', 'color', 'bgcolor', 'background-color', 'canvas-color', 'C', 5),
(10, 1, 'Which shortcut key in Windows is used to permanently delete a file without sending it to the Recycle Bin?', 'Delete', 'Shift + Delete', 'Ctrl + Delete', 'Alt + Delete', 'B', 5),

(11, 2, 'What does the acronym \"CCC\" stand for in IT literacy certifications?', 'Course on Computer Concepts', 'Computer Communication Certificate', 'Central Computer Course', 'Certificate of Computing Core', 'A', 5),
(12, 2, 'Which digital financial service allows instant fund transfers 24x7 using a Virtual Payment Address (VPA)?', 'NEFT', 'RTGS', 'UPI (Unified Payments Interface)', 'Cheque Clearing', 'C', 5),
(13, 2, 'What is the full form of OTP in modern two-factor digital authentication?', 'One Time Password', 'Online Technical Protocol', 'Official Transmission Pass', 'One Tap Pin', 'A', 5),
(14, 2, 'Which of the following is an open-source Operating System?', 'Microsoft Windows 11', 'macOS Sonoma', 'Linux Ubuntu', 'MS-DOS', 'C', 5),
(15, 2, 'What is the maximum number of digits present in an Indian Aadhaar number?', '10', '12', '14', '16', 'B', 5),
(16, 2, 'Which key combination is used to copy selected text to the clipboard in Windows?', 'Ctrl + V', 'Ctrl + C', 'Ctrl + X', 'Ctrl + Z', 'B', 5),
(17, 2, 'What does \"WWW\" stand for in Internet terminology?', 'World Wide Web', 'World Wide World', 'World Wireless Web', 'Web Wide World', 'A', 5),
(18, 2, 'Which type of malicious software disguises itself as legitimate software to trick users?', 'Firewall', 'Trojan Horse', 'Antivirus', 'Cookie', 'B', 5),
(19, 2, 'In email communication, what does the abbreviation \"BCC\" stand for?', 'Basic Carbon Copy', 'Blind Carbon Copy', 'Backup Client Contact', 'Broadcast Clean Copy', 'B', 5),
(20, 2, 'What is the key used for checking spelling and grammar in Microsoft Office / LibreOffice?', 'F1', 'F5', 'F7', 'F11', 'C', 5),

(21, 3, 'Which Excel formula correctly calculates the sum of cells from A1 to A10?', '=ADD(A1:A10)', '=SUM(A1:A10)', '=TOTAL(A1:A10)', '=COUNT(A1:A10)', 'B', 5),
(22, 3, 'What is the keyboard shortcut to undo the last action in MS Office applications?', 'Ctrl + Y', 'Ctrl + U', 'Ctrl + Z', 'Ctrl + R', 'C', 5),
(23, 3, 'Which feature in MS Word allows sending personalized letters to multiple recipients simultaneously?', 'Macro', 'Mail Merge', 'AutoCorrect', 'SmartArt', 'B', 5),
(24, 3, 'In MS PowerPoint, what is the motion effect applied to slides as they transition called?', 'Slide Transition', 'Custom Animation', 'Slide Sorter', 'Theme Effect', 'A', 5),
(25, 3, 'Which formula in Excel is used to search for a value in the leftmost column of a table?', '=HLOOKUP', '=VLOOKUP', '=SEARCH', '=FIND', 'B', 5),
(26, 3, 'What is the shortcut to save an existing document in MS Office with a new name (Save As)?', 'Ctrl + S', 'F12', 'Alt + S', 'F2', 'B', 5),
(27, 3, 'Which bar in Excel displays the contents of the active cell or the formula being edited?', 'Title Bar', 'Status Bar', 'Formula Bar', 'Task Bar', 'C', 5),
(28, 3, 'What is the shortcut key to paste copied text or objects in Microsoft Office?', 'Ctrl + P', 'Ctrl + V', 'Ctrl + C', 'Ctrl + X', 'B', 5),
(29, 3, 'Which orientation makes a document wider horizontally than it is tall vertically?', 'Portrait', 'Landscape', 'Vertical', 'Square', 'B', 5),
(30, 3, 'What is the shortcut to select all text or items in a document?', 'Ctrl + S', 'Ctrl + A', 'Ctrl + Space', 'Alt + A', 'B', 5)
ON DUPLICATE KEY UPDATE `question_text`=VALUES(`question_text`);

-- --------------------------------------------------------
-- Table: exam_attempts
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `exam_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT DEFAULT NULL,
  `exam_id` INT NOT NULL,
  `roll_number` VARCHAR(50) NOT NULL,
  `student_name` VARCHAR(120) NOT NULL,
  `course_name` VARCHAR(150) NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME DEFAULT NULL,
  `total_questions` INT NOT NULL DEFAULT 0,
  `attempted` INT NOT NULL DEFAULT 0,
  `correct_answers` INT NOT NULL DEFAULT 0,
  `wrong_answers` INT NOT NULL DEFAULT 0,
  `unattempted` INT NOT NULL DEFAULT 0,
  `total_marks` INT NOT NULL DEFAULT 0,
  `obtained_marks` INT NOT NULL DEFAULT 0,
  `percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `grade` VARCHAR(10) DEFAULT 'F',
  `status` ENUM('in_progress', 'completed', 'cancelled') DEFAULT 'in_progress',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: exam_answers
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `exam_answers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `attempt_id` INT NOT NULL,
  `question_id` INT NOT NULL,
  `selected_option` ENUM('A', 'B', 'C', 'D') DEFAULT NULL,
  `is_correct` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`attempt_id`) REFERENCES `exam_attempts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: results
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `results` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `attempt_id` INT NOT NULL UNIQUE,
  `student_id` INT DEFAULT NULL,
  `exam_id` INT NOT NULL,
  `roll_number` VARCHAR(50) NOT NULL,
  `total_marks` INT NOT NULL,
  `obtained_marks` INT NOT NULL,
  `percentage` DECIMAL(5,2) NOT NULL,
  `grade` VARCHAR(10) NOT NULL,
  `pass_status` ENUM('PASS', 'FAIL') NOT NULL,
  `date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`attempt_id`) REFERENCES `exam_attempts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: certificates
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `certificates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `certificate_number` VARCHAR(100) NOT NULL UNIQUE,
  `student_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  `result_id` INT NOT NULL,
  `student_name` VARCHAR(120) NOT NULL,
  `roll_number` VARCHAR(50) NOT NULL,
  `course_name` VARCHAR(200) NOT NULL,
  `duration` VARCHAR(100) NOT NULL,
  `percentage` DECIMAL(5,2) NOT NULL,
  `grade` VARCHAR(10) NOT NULL,
  `issue_date` DATE NOT NULL,
  `status` ENUM('active', 'revoked') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`result_id`) REFERENCES `results`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Sample Completed Attempt, Result and Certificate for Rahul Kumar (Roll No: MG202601)
INSERT INTO `exam_attempts` (`id`, `student_id`, `exam_id`, `roll_number`, `student_name`, `course_name`, `start_time`, `end_time`, `total_questions`, `attempted`, `correct_answers`, `wrong_answers`, `unattempted`, `total_marks`, `obtained_marks`, `percentage`, `grade`, `status`) VALUES
(1, 1, 1, 'MG202601', 'Rahul Kumar', 'Advanced Diploma in Computer Applications', '2026-02-15 10:00:00', '2026-02-15 10:14:20', 10, 10, 9, 1, 0, 50, 45, 90.00, 'A+', 'completed')
ON DUPLICATE KEY UPDATE `id`=VALUES(`id`);

INSERT INTO `results` (`id`, `attempt_id`, `student_id`, `exam_id`, `roll_number`, `total_marks`, `obtained_marks`, `percentage`, `grade`, `pass_status`, `date`) VALUES
(1, 1, 1, 1, 'MG202601', 50, 45, 90.00, 'A+', 'PASS', '2026-02-15')
ON DUPLICATE KEY UPDATE `id`=VALUES(`id`);

INSERT INTO `certificates` (`id`, `certificate_number`, `student_id`, `course_id`, `result_id`, `student_name`, `roll_number`, `course_name`, `duration`, `percentage`, `grade`, `issue_date`, `status`) VALUES
(1, 'MGI-2026-00001', 1, 1, 1, 'Rahul Kumar', 'MG202601', 'Advanced Diploma in Computer Applications', '12 Months', 90.00, 'A+', '2026-02-15', 'active')
ON DUPLICATE KEY UPDATE `certificate_number`=VALUES(`certificate_number`);

-- --------------------------------------------------------
-- Table: contact_messages
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'read', 'replied') DEFAULT 'unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`) VALUES
(1, 'Suresh Patel', 'suresh@example.com', '9898989898', 'Admission inquiry for ADCA course', 'Hello Manager DK Singh, I would like to know the next batch starting dates for the ADCA course in Payagpur.', 'unread')
ON DUPLICATE KEY UPDATE `id`=VALUES(`id`);