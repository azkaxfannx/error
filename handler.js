import { nanoid } from "nanoid";
import books from "./books.js";

const addBookHandler = (request, h) => {
    const {body} = request.payload;
    const newBook = Object.assign({}, body);

    newBook.insertedAt = new Date().toISOString();
    newBook.updateAt = newBook.insertedAt;
    newBook.id = nanoid(16);

    books.push(newBook);

    console.log(request.payload);

    const isSuccess = books.filter((book) => book.id === newBook.id).length > 0;

    if(isSuccess) {
        if(newBook.name !== undefined && newBook.name !== '') {
            if(newBook.pageCount <= newBook.readPage) {
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

export default addBookHandler;
