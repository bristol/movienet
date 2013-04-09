create temporary table fivek_movies as (select SQL_NO_CACHE mid
					from rated
					group by mid
					having count(*) > 5000);
select SQL_NO_CACHE M.title, M.year, avg(U.age)
from fivek_movies K, movies M, users U, rated R
where K.mid=M.mid and M.mid=R.mid and R.uid=U.uid;
