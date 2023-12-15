import { nanoid } from "nanoid";
import books from "./books.js";

const addBookHandler = (request, h) => {
    const {name, year, author, summary, publisher, pageCount, readPage, reading} = request.payload;
    
    const insertedAt = new Date().toISOString();
    const updatedAt = insertedAt;
    const id = nanoid(16);

    const newBook = {
        id,
        name,
        year,
        author,
        summary,
        publisher,
        pageCount,
        readPage,
        finished: pageCount === readPage ? true : false,
        reading,
        insertedAt,
        updatedAt,
    }

    books.push(newBook);

    const isSuccess = books.filter((book) => book.id === id).length > 0;

    if(isSuccess) {
        if(name) {
            if(readPage <= pageCount) {
                const response = h.response({
                    status: 'success',
                    message: 'Buku berhasil ditambahkan!',
                    data: {
                        noteId: newBook.id,
                    }
                })
        
                response.code(201);
                return response;
            }
            const response = h.response({
                status: 'fail',
                message: 'Gagal menambahkan buku. readPage tidak boleh lebih besar dari pageCount!',
            })
        
            response.code(400);
            return response;

        }

        const response = h.response({
            status: 'fail',
            message: 'Gagal menambahkan buku. Mohon isi nama buku!',
        })
    
        response.code(400);
        return response;

    }

    const response = h.response({
        status: 'fail',
        message: 'Buku gagal ditambahkan!',
    })

    response.code(400);
    return response;
}

const getAllBooksHandler = (request, h) => {
    const response = h.response({
        status: 'success',
        data: {
            books: books.map((book) => ({
                id: book.id,
                name: book.name,
                publisher: book.publisher,
            }))
        }
    })
    response.code(200);
    return response;
}

const getBookByIdHandler = (request, h) => {
    const {id} = request.params;

    const book = books.filter((b) => b.id === id)[0];

    if(book !== undefined) {
        return {
            status: 'success',
            data: {
                book,
            }
        }
    }

    const response = h.response({
        status: 'fail',
        message: 'Buku tidak ditemukan!',
    })
    response.code(404);
    return response;
}

const editBookByIdHandler = (request, h) => {
    const {id} = request.params;

    const {name, year, author, summary, publisher, pageCount, readPage, reading} = request.payload;

    const updatedAt = new Date().toISOString();

    const index = books.findIndex((book) => book.id === id);

    if(index !== -1) {
        if(name) {
            if(readPage <= pageCount) {
                books[index] = {
                    ...books[index],
                    name,
                    year,
                    author,
                    summary,
                    publisher,
                    pageCount,
                    readPage,
                    finished: readPage === pageCount ? true : false,
                    reading,
                    updatedAt,
                }
                const response = h.response({
                    status: 'success',
                    message: 'Buku berhasil diperbarui',
                })
            
                response.code(200);
                return response;
            }
            const response = h.response({
                status: 'fail',
                message: 'Gagal menambahkan buku. readPage tidak boleh lebih besar dari pageCount!',
            })
        
            response.code(400);
            return response;
        }
        const response = h.response({
            status: 'fail',
            message: 'Gagal menambahkan buku. Mohon isi nama buku!',
        })
    
        response.code(400);
        return response;
    }
    const response = h.response({
        status: 'fail',
        message: 'Gagal memperbarui buku. Id tidak ditemukan!',
    })

    response.code(404);
    return response;
}

const deleteBookByidHandler = (request, h) => {
    const {id} = request.params;

    const index = books.findIndex((book) => book.id === id);

    if(index !== -1) {
        books.splice(index, 1);
        const response = h.response({
            status: 'success',
            message: 'Buku berhasil dihapus',
        })

        response.code(200);
        return response;
    }
    const response = h.response({
        status: 'fail',
        message: 'Buku gagal dihapus. Id tidak ditemukan!',
    })

    response.code(404);
    return response;
}

const getAllReadingBooksByQueryHandler = (request, h) => {
    const {reading} = request.query;

    const isReading = reading === '1';

    if(isReading) {
        const response = h.response({
            status: 'success',
            message: `Berhasil mendapatkan data buku yang ${isReading ? 'sedang' : 'tidak'} dibaca`,
            data: {
                books: books.filter((book) => book.reading === isReading),
            }
        })

        response.code(200);
        return response;
    }
    
    const response = h.response({
        status: 'fail',
        message: 'Query tidak valid',
    })
    
    response.code(404);
    return response;
}

export {addBookHandler, 
    getAllBooksHandler, 
    getBookByIdHandler, 
    editBookByIdHandler, 
    deleteBookByidHandler, 
    getAllReadingBooksByQueryHandler};
