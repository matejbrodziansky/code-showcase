begin;

SELECT maintainance.create_sql_patch('20242602-01-matej.brodzinsky-VSEMSIS-1382.sql', 'matej.brodziansky');
DROP VIEW sis.view_students_mod_light;
CREATE OR REPLACE VIEW sis.view_students_mod_light AS
  SELECT a.id,
    ssp.id AS id_study_specialization,
    study_form.descrip AS descrip_study_form,
    sd.descrip AS descrip_study_domain,
    sd.id AS id_study_domain,
    a.note AS student_note,
    study.id_study_form,
    sis.get_student_study_program_id(study.id) AS id_study_program,
    sis.get_student_study_program_descrip(study.id) AS study_program,
    sis.get_student_specialization_text(study.id) AS specialization_descrip,
    ssl3.czv,
    ssl3.esc,
    ssl3.isp,
    ssl3.dr,
    ssl3.vyloucen_pod AS vylp,
    ssl3.kla,
    ssl3.sta,
    ssl3.pre,
    ssl3.exc,
    ssl3.stp,
    ssl3.st,
    ssl3.mss,
    ssl3.ms,
    ssl3.ss,
    ssl3.es,
    ssl3.olympic_scholarship AS os,
    ssl3.uby,
    ssl3.soc,
    (((((((((((((((((sis.to_char(ssl3.czv) || ':czv,'::text) || sis.to_char(ssl3.esc)) || ':esc,'::text) || sis.to_char(ssl3.isp)) || ':isp,'::text) || sis.to_char(ssl3.dr)) || ':dr,'::text) || sis.to_char(ssl3.vyloucen_pod)) || ':vylp,'::text) || sis.to_char(ssl3.kla)) || ':kla,'::text) || sis.to_char(ssl3.sta)) || ':sta,'::text) || sis.to_char(ssl3.pre)) || ':pre,'::text) || sis.to_char(ssl3.exc)) || ':exc,'::text  || sis.to_char(ssl3.stp)) || ':stp,'::text AS poz,
    ((((((((((((((sis.to_char(ssl3.st) || ':st,'::text) || sis.to_char(ssl3.mss)) || ':mss,'::text) || sis.to_char(ssl3.ms)) || ':ms,'::text) || sis.to_char(ssl3.ss)) || ':ss,'::text) || sis.to_char(ssl3.es)) || ':es,'::text) || sis.to_char(ssl3.olympic_scholarship)) || ':os,'::text) || sis.to_char(ssl3.uby)) || ':uby,'::text) || sis.to_char(ssl3.soc)) || ':soc,'::text AS stip,
    ssl3.sports_industry_id,
    sis.get_user_name(a.id) AS get_user_name,
    sg.descrip AS study_group,
    sg.id AS id_study_group,
    u.id AS id_users,
    u.username,
    a.citizenship,
    a.firstname,
    a.middlename,
    a.surname,
    a.surname_birth,
    a.birth_date,
    a.birth_certificate_no,
    a.sex,
    a.idcard_no,
    a.passport_no,
    a.nationality,
    a.member_of_council,
    a.password,
    u.active,
    u.last_logged,
    er2.id AS id_entrance_result,
    study.id AS id_student_study,
    a.deleted,
    sis.get_student_study_status(study.id) AS status,
    si.descrip AS sports_industry,
    a2.id AS id_aspirants,
    sis.get_student_study_start(study.id) AS id_study_start,
    sis.get_student_study_start_date(study.id) AS study_start,
    study_sub_form.id AS study_sub_form_id,
    study_sub_form.name AS study_sub_form_name,
    p.descrip AS phone_number,
    a2.created AS registration_date
  FROM engine.users u
    JOIN sis.users_ext a ON a.id = u.id AND a.deleted = false
    JOIN sis.student ss2 ON ss2.id = a.id
    JOIN sis.student_study study ON study.id_student = ss2.id
    JOIN sis.student_study_log ssl3 ON ssl3.id_student_study = study.id AND ssl3.active = true
    LEFT JOIN sis.entrance_result er2 ON er2.id_student_study = study.id
    LEFT JOIN sis.study_groups sg ON sg.id = ssl3.id_study_groups
    LEFT JOIN sis.aspirants a2 ON study.id = a2.id_student_study
    LEFT JOIN sis.specialization_registration sr ON sr.id_student_study = study.id
    LEFT JOIN sis.study_specialization ssp ON ssp.id = sr.id_study_specialization
    LEFT JOIN ri.sports_industry si ON si.id = ssl3.sports_industry_id
    LEFT JOIN sis.student_study__study_domain sssd ON sssd.id_student_study = study.id
    LEFT JOIN sis.study_domain sd ON sd.id = sssd.id_study_domain
    LEFT JOIN sis.study_form study_form ON study_form.id::text = study.id_study_form::text
    LEFT JOIN sis.study_sub_form study_sub_form ON study_sub_form.id = study.study_sub_form_id
    LEFT JOIN sis.contact c ON c.id_users_ext_contact = u.id
    LEFT JOIN sis.phone p ON p.id_contact = c.id;


COMMIT;
