import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { NewsTable } from "@/components/NewsTable";
import { NewsForm } from "@/components/NewsForm";
import { NewsArticle, apiService } from "@/services/api";
import { Plus, Newspaper } from "lucide-react";
import { useToast } from "@/hooks/use-toast";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";

const Index = () => {
  const { toast } = useToast();
  const [articles, setArticles] = useState<NewsArticle[]>([]);
  const [showForm, setShowForm] = useState(false);
  const [editingArticle, setEditingArticle] = useState<NewsArticle | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  // Load articles on mount
  useEffect(() => {
    loadArticles();
  }, []);

  const loadArticles = async () => {
    try {
      const data = await apiService.getNews();
      setArticles(data);
    } catch (error) {
      toast({
        title: "Error",
        description: "Failed to load articles",
        variant: "destructive",
      });
    }
  };

  const handleCreateOrUpdate = async (article: NewsArticle) => {
    setIsLoading(true);
    try {
      if (article.id) {
        await apiService.updateNews(article.id, article);
        toast({
          title: "Success",
          description: "Article updated successfully",
        });
      } else {
        await apiService.createNews(article);
        toast({
          title: "Success",
          description: "Article created successfully",
        });
      }
      
      await loadArticles();
      setShowForm(false);
      setEditingArticle(null);
    } catch (error) {
      toast({
        title: "Error",
        description: "Failed to save article",
        variant: "destructive",
      });
    } finally {
      setIsLoading(false);
    }
  };

  const handleEdit = (article: NewsArticle) => {
    setEditingArticle(article);
    setShowForm(true);
  };

  const confirmDelete = async () => {
    if (!deleteId) return;
    
    try {
      await apiService.deleteNews(deleteId);
      await loadArticles();
      toast({
        title: "Success",
        description: "Article deleted successfully",
      });
    } catch (error) {
      toast({
        title: "Error",
        description: "Failed to delete article",
        variant: "destructive",
      });
    } finally {
      setDeleteId(null);
    }
  };

  const handleCancel = () => {
    setShowForm(false);
    setEditingArticle(null);
  };

  return (
    <div className="min-h-screen bg-background">
      <div className="border-b border-border bg-card">
        <div className="container mx-auto px-6 py-6">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                <Newspaper className="w-6 h-6 text-primary-foreground" />
              </div>
              <div>
                <h1 className="text-2xl font-bold">News Management</h1>
                <p className="text-sm text-muted-foreground">Manage your news articles</p>
              </div>
            </div>
            {!showForm && (
              <Button onClick={() => setShowForm(true)} size="lg">
                <Plus className="w-5 h-5 mr-2" />
                New Article
              </Button>
            )}
          </div>
        </div>
      </div>

      <div className="container mx-auto px-6 py-8">
        {showForm ? (
          <NewsForm
            article={editingArticle}
            onSubmit={handleCreateOrUpdate}
            onCancel={handleCancel}
            isLoading={isLoading}
          />
        ) : (
          <NewsTable
            articles={articles}
            onEdit={handleEdit}
            onDelete={setDeleteId}
          />
        )}
      </div>

      <AlertDialog open={deleteId !== null} onOpenChange={() => setDeleteId(null)}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Are you sure?</AlertDialogTitle>
            <AlertDialogDescription>
              This action cannot be undone. This will permanently delete the article.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction onClick={confirmDelete} className="bg-destructive text-destructive-foreground hover:bg-destructive/90">
              Delete
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
};

export default Index;
