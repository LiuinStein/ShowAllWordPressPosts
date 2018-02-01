# 0x00 About

This program used to directly get all WordPress posts from the raw data in the database. 

# 0x01 How to use it

1. Download the PHP code from the repository.
2. Change the database connection code to fit your private database. You will find it at `getData` function. Make sure the user can access the **whole** WordPress database (SELECT privilege at least).If you had changed the WordPress table prefix, don't forget to change the SQL statement at `getData` function.
3. Put the code into your web-root dictionary, and browse it in your browser.

# 0x02 Footnote

1. The file `test.sql` is **useless** for the code. The file includes a **good formatting** SQL statement excerpt from the PHP code.

