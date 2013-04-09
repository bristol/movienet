select SQL_NO_CACHE distinct M.title, M.year 
from people P, acted_in A, movies M 
where P.name like 'Pitt, Brad' and P.pid = A.pid and M.mid = A.mid;
