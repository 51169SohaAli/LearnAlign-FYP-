TYPE=VIEW
query=select `obe`.`modules`.`RegisteredCourseName` AS `RegisteredCourseName`,`obe`.`modules`.`RegisteredCourseNameShort` AS `RegisteredCourseNameShort` from `obe`.`modules` where `obe`.`modules`.`RegisteredCourseName` in (\'Database Systems\',\'Programming Fundamentals\',\'Software Requirement Engineering\',\'Human Computer Interaction\',\'Introduction to Machine Learning\',\'Stats and Probability\',\'Operating Systems\',\'Computer Networks\',\'Software Quality Engineering\',\'Software Re Engineering\')
md5=38fbaeb75dd694e1b6034dc687763706
updatable=1
algorithm=0
definer_user=root
definer_host=localhost
suid=2
with_check_option=0
timestamp=0001744457871618773
create-version=2
source=SELECT RegisteredCourseName, RegisteredCourseNameShort\nFROM modules\nWHERE RegisteredCourseName IN (\n    \'Database Systems\',\n    \'Programming Fundamentals\',\n    \'Software Requirement Engineering\',\n    \'Human Computer Interaction\',\n    \'Introduction to Machine Learning\',\n    \'Stats and Probability\',\n    \'Operating Systems\',\n    \'Computer Networks\',\n    \'Software Quality Engineering\',\n    \'Software Re Engineering\'\n)
client_cs_name=utf8mb4
connection_cl_name=utf8mb4_unicode_ci
view_body_utf8=select `obe`.`modules`.`RegisteredCourseName` AS `RegisteredCourseName`,`obe`.`modules`.`RegisteredCourseNameShort` AS `RegisteredCourseNameShort` from `obe`.`modules` where `obe`.`modules`.`RegisteredCourseName` in (\'Database Systems\',\'Programming Fundamentals\',\'Software Requirement Engineering\',\'Human Computer Interaction\',\'Introduction to Machine Learning\',\'Stats and Probability\',\'Operating Systems\',\'Computer Networks\',\'Software Quality Engineering\',\'Software Re Engineering\')
mariadb-version=100432
