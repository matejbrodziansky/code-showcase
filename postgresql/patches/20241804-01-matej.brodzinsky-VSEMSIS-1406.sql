begin;

SELECT maintainance.create_sql_patch('20241804-01-matej.brodzinsky-VSEMSIS-1406.sql', 'matej.brodziansky');


-- Insert new course type
INSERT INTO ri.course_type (name) VALUES ('Nanostudium VŠEM');

--  En Translate ;
INSERT INTO ri.course_type_translation (id, lang, name)
SELECT id, 'en', 'Nano Degree at VŠEM'
FROM ri.course_type
WHERE name = 'Nanostudium VŠEM';

-- Insert new course type
INSERT INTO ri.course_type (name) VALUES ('Profesní certifikáty VŠEM');

--  En Translate ;
INSERT INTO ri.course_type_translation (id, lang, name)
SELECT id, 'en', 'Professional Certificates at VŠEM'
FROM ri.course_type
WHERE name = 'Profesní certifikáty VŠEM';

DELETE FROM ri.course_type_translation
WHERE id IN (SELECT id FROM ri.course_type WHERE name='Profesní kurz zdarma');

COMMIT;
