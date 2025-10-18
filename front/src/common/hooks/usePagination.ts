import {useState} from "react";

export interface Pagination {
    count: number;
    page: number;
    perPage: number;
}

export function usePagination(initialPerPage: number = 10) {
    const [pagination, setPagination] = useState<Pagination>({
        count: 0,
        page: 1,
        perPage: initialPerPage,
    });

    const resetPagination = (perPage: number = initialPerPage) => {
        setPagination({count: 0, page: 1, perPage});
    };

    const setPage = (page: number) => {
        setPagination(prev => ({...prev, page}));
    };

    const setCount = (count: number) => {
        setPagination(prev => ({...prev, count}));
    };

    const nextPage = () => {
        setPagination(prev => ({...prev, page: prev.page + 1}));
    };

    return {pagination, setPagination, resetPagination, setPage, setCount, nextPage};
}
