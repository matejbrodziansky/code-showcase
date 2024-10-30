begin;

SELECT maintainance.create_sql_patch('20240905-01-matej.brodzinsky-VSEMSIS-1409.sql', 'matej.brodziansky');

CREATE
OR REPLACE VIEW "export"."view_emails"
AS
SELECT view_emails_temp.id,
       view_emails_temp.username,
       CASE
           WHEN ((view_emails_temp.username)::text = (view_emails_temp.email_forward)::text) THEN NULL::character varying
            ELSE view_emails_temp.email_forward
END
AS email_forward,
    view_emails_temp.password,
    view_emails_temp.type
   FROM maintainance.view_emails_temp
  WHERE (NOT ((view_emails_temp.username)::text IN ( SELECT view_email_aliases.alias
           FROM export.view_email_aliases)))
AND view_emails_temp.username LIKE 'uchazec%@infovsem.cz';

COMMIT;
