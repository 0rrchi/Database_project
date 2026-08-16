-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2026 at 06:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ewu_ta_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` varchar(20) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `Password`, `FirstName`, `LastName`, `Email`) VALUES
('ADM001', 'admin123', 'System', 'Administrator', 'admin1@ewu.edu.bd'),
('ADM002', 'admin456', 'Department', 'Administrator', 'admin2@ewu.edu.bd');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `CourseID` int(11) NOT NULL,
  `CourseCode` varchar(20) NOT NULL,
  `CourseTitle` varchar(100) NOT NULL,
  `Credit` decimal(2,1) NOT NULL,
  `DepartmentID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`CourseID`, `CourseCode`, `CourseTitle`, `Credit`, `DepartmentID`) VALUES
(1, 'CSE302', 'Database Management System', 4.5, 1),
(2, 'EEE102', 'Basic Circuit Design', 4.0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `DepartmentID` int(11) NOT NULL,
  `DepartmentName` varchar(100) NOT NULL,
  `FloorNo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`DepartmentID`, `DepartmentName`, `FloorNo`) VALUES
(1, 'CSE', '3'),
(2, 'EEE', '2');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `FacultyID` varchar(20) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Designation` varchar(50) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `IsActivated` tinyint(1) DEFAULT 0,
  `RecruitingTA` tinyint(1) NOT NULL DEFAULT 0,
  `DepartmentID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`FacultyID`, `FirstName`, `LastName`, `Email`, `Designation`, `Password`, `IsActivated`, `RecruitingTA`, `DepartmentID`) VALUES
('Fac001', 'Antu', 'Chowdhury', 'antu@faculty.ewu.edu', 'Lecturer', 'antu123', 1, 0, 1),
('Fac002', 'Kazi Mehedi', 'Mohammad', 'mehedi@faculty.ewu.edu', 'Lecturer', NULL, 0, 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_course`
--

CREATE TABLE `faculty_course` (
  `FacultyCourseID` int(11) NOT NULL,
  `FacultyID` varchar(20) NOT NULL,
  `CourseID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_course`
--

INSERT INTO `faculty_course` (`FacultyCourseID`, `FacultyID`, `CourseID`) VALUES
(1, 'Fac001', 1),
(2, 'Fac002', 2);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `StudentID` varchar(20) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `DateOfBirth` date NOT NULL,
  `CGPA` decimal(3,2) NOT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `IsActivated` tinyint(1) DEFAULT 0,
  `DepartmentID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`StudentID`, `FirstName`, `LastName`, `Email`, `Phone`, `DateOfBirth`, `CGPA`, `Password`, `IsActivated`, `DepartmentID`) VALUES
('2024-3-60-236', 'Sanzida', 'Rahman', '2024-3-60-236@std.ewu.edu', '01700006950', '2003-09-24', 4.00, NULL, 0, 2),
('2024-3-60-250', 'Taif', 'Chowdhury', '2024-3-60-250@std.ewu.edu', '01707066123', '2005-08-30', 4.00, 'student123', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `student_course`
--

CREATE TABLE `student_course` (
  `StudentCourseID` int(11) NOT NULL,
  `StudentID` varchar(20) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `SemesterName` enum('Spring','Summer','Fall') NOT NULL,
  `Year` year(4) NOT NULL,
  `Grade` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_course`
--

INSERT INTO `student_course` (`StudentCourseID`, `StudentID`, `CourseID`, `SemesterName`, `Year`, `Grade`) VALUES
(1, '2024-3-60-250', 1, 'Summer', '2026', '4'),
(2, '2024-3-60-236', 2, 'Spring', '2026', '4');

-- --------------------------------------------------------

--
-- Table structure for table `ta_application`
--

CREATE TABLE `ta_application` (
  `ApplicationID` int(11) NOT NULL,
  `StudentID` varchar(20) NOT NULL,
  `FacultyID` varchar(20) NOT NULL,
  `ResumeFile` varchar(255) DEFAULT NULL,
  `CoverLetter` varchar(255) DEFAULT NULL,
  `ApplicationDate` date NOT NULL,
  `Status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ta_application`
--

INSERT INTO `ta_application` (`ApplicationID`, `StudentID`, `FacultyID`, `ResumeFile`, `CoverLetter`, `ApplicationDate`, `Status`) VALUES
(1, '2024-3-60-250', 'Fac001', '1786899066_CV_6a81ea7a5c9b4.pdf', '1786899066_CoverLetter_6a81ea7a5cbe3.pdf', '2026-08-16', 'Rejected');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`CourseID`),
  ADD UNIQUE KEY `CourseCode` (`CourseCode`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`DepartmentID`),
  ADD UNIQUE KEY `DepartmentName` (`DepartmentName`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`FacultyID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- Indexes for table `faculty_course`
--
ALTER TABLE `faculty_course`
  ADD PRIMARY KEY (`FacultyCourseID`),
  ADD UNIQUE KEY `unique_faculty_course` (`FacultyID`,`CourseID`),
  ADD KEY `CourseID` (`CourseID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`StudentID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- Indexes for table `student_course`
--
ALTER TABLE `student_course`
  ADD PRIMARY KEY (`StudentCourseID`),
  ADD UNIQUE KEY `unique_student_course` (`StudentID`,`CourseID`,`SemesterName`,`Year`),
  ADD KEY `CourseID` (`CourseID`);

--
-- Indexes for table `ta_application`
--
ALTER TABLE `ta_application`
  ADD PRIMARY KEY (`ApplicationID`),
  ADD UNIQUE KEY `unique_student_faculty_application` (`StudentID`,`FacultyID`),
  ADD KEY `FacultyID` (`FacultyID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `CourseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faculty_course`
--
ALTER TABLE `faculty_course`
  MODIFY `FacultyCourseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_course`
--
ALTER TABLE `student_course`
  MODIFY `StudentCourseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ta_application`
--
ALTER TABLE `ta_application`
  MODIFY `ApplicationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `faculty_course`
--
ALTER TABLE `faculty_course`
  ADD CONSTRAINT `faculty_course_ibfk_1` FOREIGN KEY (`FacultyID`) REFERENCES `faculty` (`FacultyID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `faculty_course_ibfk_2` FOREIGN KEY (`CourseID`) REFERENCES `course` (`CourseID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_course`
--
ALTER TABLE `student_course`
  ADD CONSTRAINT `student_course_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `student` (`StudentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_course_ibfk_2` FOREIGN KEY (`CourseID`) REFERENCES `course` (`CourseID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ta_application`
--
ALTER TABLE `ta_application`
  ADD CONSTRAINT `ta_application_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `student` (`StudentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ta_application_ibfk_2` FOREIGN KEY (`FacultyID`) REFERENCES `faculty` (`FacultyID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
