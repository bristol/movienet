create temporary table bad_kids as (select SQL_NO_CACHE U.uid
					from users U, rated R, movies M, has_mpaa H, mpaaratings A
					where U.uid=R.uid and R.mid=M.mid and M.mid=H.mid and H.mpaaid=A.mpaaid and A.abbreviation like 'NC-17' and U.age <= 17);
select SQL_NO_CACHE U.email, U.name, U.age, U.location
from bad_kids B, users U
where B.uid=U.uid;
