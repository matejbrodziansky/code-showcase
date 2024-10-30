BEGIN;

SELECT maintainance.create_sql_patch('20231030-01-matej.brodziansky-VSEMSIS-1343', 'matej.brodziansky');


CREATE OR REPLACE VIEW export.view_users_active_directory_office365_students AS
  SELECT ue.id,
    eu.username as email,
    ue.firstname,
    ue.surname AS lastname,
    eu.username::text AS login,
    ue.password,
    --export.get_students_groups(l.id, ','::text, true)::text AS "group",
    'student'::text AS "group",
    'student'::text AS ou,
    eu.id::text as samaccountname,
    'student'::text AS employee_type,
    se.descrip as email_forward
  FROM sis.users_ext l
    JOIN sis.users_ext ue ON ue.id = l.id
    JOIN engine.users eu ON eu.id = ue.id
    JOIN sis.student s ON s.id=ue.id
    JOIN sis.contact sc ON ue.id = sc.id_users_ext_contact
    JOIN sis.email se ON sc.id = se.id_contact
  WHERE
    ue.deleted=false AND
    eu.id NOT IN (select id from export.view_computer_sharing_narozni_employees) AND
    eu.id NOT IN (select id from export.view_computer_sharing_narozni_lectors) AND
    s.id IN (select id_student from sis.student_study where id IN (select id_student_study from sis.student_study_log where status='student_pre_graduate' OR status='studying')) AND
    eu.active = true AND
    -- vynecham testovaci ucty
    ue.id not in (0, 1, 2, 3, 31530, 31076, 34403, 34404, 34406, 34405, 34872, 31077, 31251)
;



CREATE OR REPLACE VIEW export.view_users_active_directory_office365_lectors AS
  SELECT ue.id,
    eu.username as email,
    ue.firstname,
    ue.surname AS lastname,
    eu.username::text AS login,
    ue.password,
    'lector'::text AS "group",
    'employee'::text AS ou,
    "substring"(eu.username::text, 0, "position"(eu.username::text, '@'::text)) samaccountname,
    'employee'::text AS employee_type,
    se.descrip as email_forward
  FROM sis.lector l
    JOIN sis.users_ext ue ON ue.id = l.id
    JOIN engine.users eu ON eu.id = ue.id
    LEFT JOIN sfext.employee e ON e.id = l.id
    JOIN sis.contact sc ON ue.id = sc.id_users_ext_contact
    JOIN sis.email se ON sc.id = se.id_contact
  WHERE
    ue.deleted=false AND
    e.id IS NULL AND
    eu.active = true AND
    ue.id not in (0, 1, 2, 3, 31530, 31076, 34403, 34404, 34406, 34405, 34872, 31077, 31251)
;

CREATE OR REPLACE VIEW export.view_users_active_directory_office365_employees AS
  SELECT employee.id,
    eu.username as email,
    ue.firstname,
    ue.surname AS lastname,
    eu.username::text AS login,
    ue.password,
    CASE
    WHEN l.id IS NULL THEN 'employee'::text
    ELSE 'lector, employee'::text
    END AS "group",
    'employee'::text AS ou,
    "substring"(eu.username::text, 0, "position"(eu.username::text, '@'::text)) samaccountname,
    'employee'::text AS employee_type,
    se.descrip as email_forward
  FROM sfext.employee employee
    LEFT JOIN sis.lector l ON l.id = employee.id
    JOIN engine.users eu ON employee.id = eu.id
    JOIN sis.users_ext ue ON ue.id = eu.id
    JOIN sis.contact sc ON ue.id = sc.id_users_ext_contact
    JOIN sis.email se ON sc.id = se.id_contact
  WHERE
    ue.deleted=false AND
    employee.access_to_domain_vsem = true AND
    eu.active = true AND
    -- krome testovacich nezivych uctu
    employee.id not in (0, 1, 2, 3, 31530, 31076, 34403, 34404, 34406, 34405, 34872, 31077, 31251)
;


CREATE OR REPLACE VIEW export.view_users_active_directory_office365_aspirants AS
SELECT
  ue.id,
  eu.username as email,
  ue.firstname,
  ue.surname AS lastname,
  (eu.username)::text AS login,
  ue.password,
  'aspirant'::text AS "group",
  'aspirant'::text AS ou,
  (eu.id)::text AS samaccountname,
  'aspirant'::text AS employee_type,
  se.descrip as email_forward
FROM
  sis.users_ext l
  JOIN sis.users_ext ue ON ue.id = l.id
  JOIN engine.users eu ON eu.id = ue.id
  JOIN sis.student s ON s.id = ue.id
  JOIN sis.contact sc ON ue.id = sc.id_users_ext_contact
  JOIN sis.email se ON sc.id = se.id_contact
WHERE
  ue.deleted = false
  AND NOT eu.id IN (SELECT view_computer_sharing_narozni_employees.id FROM export.view_computer_sharing_narozni_employees)
  AND NOT eu.id IN (SELECT view_computer_sharing_narozni_lectors.id FROM export.view_computer_sharing_narozni_lectors)
  AND NOT eu.id IN (SELECT view_computer_sharing_narozni_students.id FROM export.view_computer_sharing_narozni_students)
  AND NOT eu.id IN (SELECT view_computer_sharing_narozni_graduates.id FROM export.view_computer_sharing_narozni_graduates)
  AND s.id IN(
    SELECT
      student_study.id_student
    FROM
      sis.student_study
    WHERE
      student_study.id IN(
        SELECT
          student_study_log.id_student_study
        FROM
          sis.student_study_log
        WHERE
          (student_study_log.status)::text = ANY (ARRAY['aspirant'::text, 'aspirant_admited'::text, 'aspirant_fill_condition'::text, 'aspirant_ready'::text])
      )
  )
  AND eu.active = true
  AND ue.id <> ALL (ARRAY[0, 1, 2, 3, 31530, 31076, 34403, 34404, 34406, 34405, 34872, 31077, 31251]);





create or replace view export.view_users_active_directory_office365_graduates
            (id, email, firstname, lastname, login, password, "group", ou, samaccountname, employee_type) as
SELECT ue.id,
       eu.username as email,
       ue.firstname,
       ue.surname        AS lastname,
       eu.username::text AS login,
       ue.password,
       'graduate'::text  AS "group",
       'graduate'::text  AS ou,
       eu.id::text       AS samaccountname,
       'graduate'::text  AS employee_type,
       se.descrip as email_forward
FROM sis.users_ext l
         JOIN sis.users_ext ue ON ue.id = l.id
         JOIN engine.users eu ON eu.id = ue.id
         JOIN sis.student s ON s.id = ue.id
         JOIN sis.contact sc ON ue.id = sc.id_users_ext_contact
         JOIN sis.email se ON sc.id = se.id_contact
WHERE ue.deleted = false
  AND NOT (eu.id IN (SELECT view_computer_sharing_narozni_employees.id
                     FROM export.view_computer_sharing_narozni_employees))
  AND NOT (eu.id IN (SELECT view_computer_sharing_narozni_lectors.id
                     FROM export.view_computer_sharing_narozni_lectors))
  AND NOT (eu.id IN (SELECT view_computer_sharing_narozni_students.id
                     FROM export.view_computer_sharing_narozni_students))
  AND (s.id IN (SELECT student_study.id_student
                FROM sis.student_study
                WHERE (student_study.id IN (SELECT student_study_log.id_student_study
                                            FROM sis.student_study_log
                                            WHERE student_study_log.status::text = 'graduate s'::text))))
  AND eu.active = true
  AND (ue.id <> ALL (ARRAY [0, 1, 2, 3, 31530, 31076, 34403, 34404, 34406, 34405, 34872, 31077, 31251]));


CREATE OR REPLACE VIEW "export"."view_users_active_directory_office365" AS
SELECT
  view_users_active_directory_office365_employees.id,
  view_users_active_directory_office365_employees.email,
  view_users_active_directory_office365_employees.email_forward,
  view_users_active_directory_office365_employees.firstname,
  view_users_active_directory_office365_employees.lastname,
  view_users_active_directory_office365_employees.login,
  view_users_active_directory_office365_employees.password,
  view_users_active_directory_office365_employees."group",
  view_users_active_directory_office365_employees.ou,
  view_users_active_directory_office365_employees.samaccountname,
  view_users_active_directory_office365_employees.employee_type
FROM
  export.view_users_active_directory_office365_employees
UNION
SELECT
  view_users_active_directory_office365_lectors.id,
  view_users_active_directory_office365_lectors.email,
  view_users_active_directory_office365_lectors.email_forward,
  view_users_active_directory_office365_lectors.firstname,
  view_users_active_directory_office365_lectors.lastname,
  view_users_active_directory_office365_lectors.login,
  view_users_active_directory_office365_lectors.password,
  view_users_active_directory_office365_lectors."group",
  view_users_active_directory_office365_lectors.ou,
  view_users_active_directory_office365_lectors.samaccountname,
  view_users_active_directory_office365_lectors.employee_type
FROM
  export.view_users_active_directory_office365_lectors
UNION
SELECT
  view_users_active_directory_office365_aspirants.id,
  view_users_active_directory_office365_aspirants.email,
  view_users_active_directory_office365_aspirants.email_forward,
  view_users_active_directory_office365_aspirants.firstname,
  view_users_active_directory_office365_aspirants.lastname,
  view_users_active_directory_office365_aspirants.login,
  view_users_active_directory_office365_aspirants.password,
  view_users_active_directory_office365_aspirants."group",
  view_users_active_directory_office365_aspirants.ou,
  view_users_active_directory_office365_aspirants.samaccountname,
  view_users_active_directory_office365_aspirants.employee_type
FROM
  export.view_users_active_directory_office365_aspirants
UNION
SELECT
  view_users_active_directory_office365_students.id,
  view_users_active_directory_office365_students.email,
  view_users_active_directory_office365_students.email_forward,
  view_users_active_directory_office365_students.firstname,
  view_users_active_directory_office365_students.lastname,
  view_users_active_directory_office365_students.login,
  view_users_active_directory_office365_students.password,
  view_users_active_directory_office365_students."group",
  view_users_active_directory_office365_students.ou,
  view_users_active_directory_office365_students.samaccountname,
  view_users_active_directory_office365_students.employee_type
FROM
  export.view_users_active_directory_office365_students
UNION
SELECT
  view_users_active_directory_office365_graduates.id,
  view_users_active_directory_office365_graduates.email,
  view_users_active_directory_office365_graduates.email_forward,
  view_users_active_directory_office365_graduates.firstname,
  view_users_active_directory_office365_graduates.lastname,
  view_users_active_directory_office365_graduates.login,
  view_users_active_directory_office365_graduates.password,
  view_users_active_directory_office365_graduates."group",
  view_users_active_directory_office365_graduates.ou,
  view_users_active_directory_office365_graduates.samaccountname,
  view_users_active_directory_office365_graduates.employee_type
FROM
  export.view_users_active_directory_office365_graduates;



CREATE OR REPLACE FUNCTION "export"."create_xml_for_users_active_directory_office365"() RETURNS text LANGUAGE PLPGSQL
AS
$$

declare
 radek record;
 xmlstring text;
begin

 xmlstring = '';
 xmlstring = '<Objs Version="1.1" xmlns="http://schemas.microsoft.com/powershell/2004/04">' ||chr(10);

 for radek in
  select
      '    <Obj RefId="RefId-0">'||chr(10)
    ||'        <MS>' ||chr(10)
    ||'            <S N="employeetype">' || view_users_active_directory_office365.employee_type || '</S>' ||chr(10)
    ||'            <S N="userprincipalname">' || view_users_active_directory_office365.login || '</S>' ||chr(10)
    ||'            <S N="samaccountname">' || view_users_active_directory_office365.samaccountname || '</S>' ||chr(10)
    ||'            <S N="userpassword">' || export.convert_xml_entities(view_users_active_directory_office365.password) || '</S>' ||chr(10)
    ||'            <S N="ou">' || view_users_active_directory_office365.ou || '</S>' ||chr(10)
    ||'            <S N="givenname">' || firstname || '</S>' ||chr(10)
    ||'            <S N="sn">' || lastname || '</S>' ||chr(10)
    ||'            <S N="emailforward">' || view_users_active_directory_office365.email_forward || '</S>' ||chr(10)
    ||'            <S N="groups">' || view_users_active_directory_office365.group || '</S>' ||chr(10)
    ||'            <S N="enabled">' || 'True' || '</S>' ||chr(10)
    ||'            <S N="notes">' || id || '</S>' ||chr(10)
    ||'        </MS>' ||chr(10)
    ||'    </Obj>' ||chr(10)
   as line_to_xml
  from export.view_users_active_directory_office365
 loop
  xmlstring = xmlstring || radek.line_to_xml;
 end loop;
 xmlstring = xmlstring || '</Objs>';
 return xmlstring;
 end;

$$;

COMMIT;
