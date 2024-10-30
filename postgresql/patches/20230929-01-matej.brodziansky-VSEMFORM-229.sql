BEGIN;

--info o patchi do db
SELECT maintainance.create_sql_patch('20230929-01-matej.brodziansky-VSEMFORM-229.sql', 'matej.brodziansky');

-- rozsireni funkce o roli 'guest'
CREATE OR REPLACE FUNCTION sis.get_user_roles(_engine_user_id integer)
  RETURNS text[] AS
$BODY$

declare
  _roles text[] default '{}';
  _id_temp integer;
  _code varchar;

begin
  -- administrator zatim vracet neumime

  -- studijni ma SO jako code v sfext.employee
  _id_temp = null;

  SELECT et.role INTO _code
  FROM sfext.employee e
    JOIN engine.users u ON u.id = e.id AND u.active = TRUE
    JOIN sfext.employee_type et ON et.id = e.type_id
  WHERE e.id = _engine_user_id;

  IF (_code IS NOT NULL) THEN
    _roles := array_append(_roles, 'zamestnanec');
    _roles := array_append(_roles, _code::text);
  end if;

  -- lektor ma zaznam v sis.lector
  _id_temp = null;
  select id, lector_status_code into _id_temp, _code from sis.lector where id=_engine_user_id AND lector_status_code!='lektor_x';
  if(_id_temp is not null) THEN
    _roles := array_append(_roles, 'lektor');
  end if;

  -- student bude mit jeden ze stavu 'student_pre_graduate', 'studying' viz https://dev.imatic.cz/vsem-sis-user/wiki/File:Stavy_atributy_studenti.xls
  _id_temp = null;
  select sssl.id into _id_temp from sis.student_study_log sssl join sis.student_study sss on sss.id=sssl.id_student_study where sss.id_student=_engine_user_id AND sssl.status IN ('student_pre_graduate', 'studying') AND sssl.active=true limit 1;
  if (_id_temp is not null) THEN
    _roles := array_append(_roles, 'student');
  end if;
  _id_temp = null;
  select sssl.id into _id_temp from sis.student_study_log sssl join sis.student_study sss on sss.id=sssl.id_student_study where sss.id_student=_engine_user_id AND sssl.status IN ('graduate', 'graduate s') AND sssl.active=true limit 1;
  if (_id_temp is not null) THEN
    _roles := array_append(_roles, 'absolvent');
  end if;

  -- uchazec bude mit jeden ze stavu  viz https://dev.imatic.cz/vsem-sis-user/wiki/File:Stavy_atributy_studenti.xls
  _id_temp = null;
  select sssl.id into _id_temp from sis.student_study_log sssl join sis.student_study sss on sss.id=sssl.id_student_study where sss.id_student=_engine_user_id AND sssl.status IN ('aspirant', 'aspirant_admited', 'aspirant_fill_condition', 'aspirant_ready') AND sssl.active=true limit 1;
  if (_id_temp is not null) THEN
    _roles := array_append(_roles, 'uchazec');
  end if;

  -- Zde prověřte, zda má uživatel roli "guest" na základě existence záznamu v tabulce sis.guest.
  _id_temp = NULL;
   SELECT id INTO _id_temp FROM sis.guest WHERE id = _engine_user_id;
   IF (_id_temp IS NOT NULL) THEN
     _roles := array_append(_roles, 'guest');
   END IF;

  -- uzivatele, kteri nejsou - student || lektor || guest ||zamestnanec maji prirazenou roli 'registered'
  _id_temp = NULL;

  SELECT u.id INTO _id_temp
  FROM engine.users u
    LEFT JOIN sfext.employee e ON e.id = u.id
    LEFT JOIN sis.users_ext ue ON ue.id = u.id
    LEFT JOIN sis.student s ON s.id = ue.id
    LEFT JOIN sis.lector l ON l.id = ue.id
    LEFT JOIN sis.guest g ON g.id = ue.id
  WHERE u.id = _engine_user_id AND (e.id IS NULL AND s.id IS NULL AND l.id IS NULL AND g.id IS NULL);

  IF _id_temp IS NOT NULL THEN
    _roles := array_append(_roles, 'registered');
  END IF;

  return _roles;
end;

$BODY$
LANGUAGE plpgsql VOLATILE COST 100;
COMMENT ON FUNCTION sis.get_user_roles(integer) IS 'Dokumentace viz https://imatic.atlassian.net/wiki/pages/viewpage.action?pageId=33357840 | Author: Jan Chrastina';

COMMIT;