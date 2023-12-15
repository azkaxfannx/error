import { addBookHandler, 
    deleteBookByidHandler, 
    editBookByIdHandler, 
    getAllBooksHandler, 
    getAllReadingBooksByQueryHandler, 
    getBookByIdHandler } from './handler.js';

const routes = [
    {
        method: 'post',
        path: '/books',
        handler: addBookHandler,
    },
    {
        method: 'get',
        path: '/books',
        handler: getAllBooksHandler,
    },
    {
        method: 'get',
        path: '/books/{id}',
        handler: getBookByIdHandler,
    },
    {
        method: 'put',
        path: '/books/{id}',
        handler: editBookByIdHandler,
    },
    {
        method: 'delete',
        path: '/books/{id}',
        handler: deleteBookByidHandler,
    },
    {
        method: 'get',
        path: '/books',
        query: {reading: Joi.boolean()},
        handler: getAllReadingBooksByQueryHandler
    }
]

export default routes;
