import { Button } from "@/components/ui/button";
import { ChevronLeft, ChevronRight } from "lucide-react";

interface PaginationProps {
    currentPage: number;
    lastPage: number;
    onPageChange: (page: number) => void;
}

export function Pagination({ currentPage, lastPage, onPageChange }: PaginationProps) {
    const pages = Array.from({ length: lastPage }, (_, i) => i + 1);
    const visiblePages = pages.filter(page => {
        const distance = Math.abs(page - currentPage);
        return distance <= 2 || page === 1 || page === lastPage;
    });

    return (
        <div className="flex items-center gap-1">
            <Button
                variant="outline"
                size="sm"
                onClick={() => onPageChange(currentPage - 1)}
                disabled={currentPage === 1}
            >
                <ChevronLeft className="h-4 w-4" />
            </Button>

            {visiblePages.map((page, index) => {
                const showEllipsis = index > 0 && page - visiblePages[index - 1] > 1;
                return (
                    <div key={page} className="flex items-center">
                        {showEllipsis && (
                            <span className="px-2 text-muted-foreground">...</span>
                        )}
                        <Button
                            variant={currentPage === page ? "default" : "outline"}
                            size="sm"
                            onClick={() => onPageChange(page)}
                        >
                            {page}
                        </Button>
                    </div>
                );
            })}

            <Button
                variant="outline"
                size="sm"
                onClick={() => onPageChange(currentPage + 1)}
                disabled={currentPage === lastPage}
            >
                <ChevronRight className="h-4 w-4" />
            </Button>
        </div>
    );
} 