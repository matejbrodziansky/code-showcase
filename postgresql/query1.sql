-- ISSUE export do XLS
-- XLS má obsahovat všechny IDS (sloupec A ) které aktuálně mají stav Absolvent X nebo Absolvent S
-- XLS má obsahovat poznámky (sloupec B ), které byly napsané při změně na stav Absolvent S a to nehledě na to jaký stav má studium nyní.

SELECT DISTINCT ON (ssll.id_student_study)
    ssll.id_student_study AS IDS,
    CASE
        WHEN ssll.status = 'graduate' THEN ssll2.note
        ELSE ssll2.note
    END AS poznámka
FROM
    sis.student_study_log_log ssll
LEFT JOIN sis.student_study_log_log ssll2
    ON ssll.id_student_study = ssll2.id_student_study
    AND ssll2.status = 'graduate s'
WHERE
    ssll.status IN (
        'graduate s',
        'graduate'
    )
ORDER BY
    ssll.id_student_study,
    CASE
        WHEN ssll.status = 'graduate' THEN 1
        ELSE 2
    END,
    ssll.created DESC
;