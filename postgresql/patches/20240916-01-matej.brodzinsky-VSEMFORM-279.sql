begin;

SELECT maintainance.create_sql_patch('20240916-01-matej.brodzinsky-VSEMFORM-279.sql', 'matej.brodziansky');

CREATE OR REPLACE VIEW "sis"."student_list_phone_calls" AS
SELECT DISTINCT ON (u.id)
    u.id AS id_users,
    ue.firstname,
    ue.surname,
    p.descrip AS phone,
    e.descrip AS email,
    u.active,
    ssl.exc,
    ssl.kla,
    ssl.sta,
    ssl.pre,
    --ssl.act,
    --ssl.opn,
    ssl.created
FROM engine.users u
    LEFT JOIN sis.users_ext ue ON u.id = ue.id
    LEFT JOIN engine.users eu ON u.id = eu.id
    LEFT JOIN sis.contact contact ON contact.id_users_ext_contact = ue.id
    LEFT JOIN sis.phone p ON p.id_contact = contact.id
    LEFT JOIN sis.email e ON e.id_contact = contact.id AND e.default_email = true
    JOIN sis.student s ON s.id  = ue.id
    JOIN sis.student_study ss ON ss.id_student = s.id
    JOIN sis.student_study_log ssl ON ssl.id_student_study = ss.id
WHERE
    u.active = true
  AND e.descrip NOT ILIKE '%@vsem.cz'
  AND ssl.status IN (
    'studying',
    'student_pre_graduate'
    )
  AND eu.active = true
ORDER BY
    u.id,
    ssl.created DESC;

COMMIT;
